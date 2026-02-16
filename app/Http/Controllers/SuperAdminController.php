<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditTrail;
use App\Models\Notification;
use App\Support\AuditDictionary;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\View\ViewException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SuperAdminController extends Controller
{
    public function index(): View
    {
        // Show everyone except superadmin
        $users = User::where('role', '!=', 'superadmin')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required', 'string', 'lowercase', 'max:255', 'email:rfc,dns', 'unique:users,email'],
        ], [
            'name.required' => 'Please enter the full name.',
            'name.max' => 'Full name must be 255 characters or fewer.',
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must be 255 characters or fewer.',
            'email.unique' => 'This email address is already in use.',
        ]);

        $realtimeEmailCheck = $this->verifyEmailMailboxRealtime($data['email']);
        if (!$realtimeEmailCheck['ok']) {
            return $this->respondStoreError(
                $request,
                $realtimeEmailCheck['message'],
                422,
                $realtimeEmailCheck['error_code']
            );
        }

        if (!Schema::hasColumn('users', 'must_change_password')) {
            return $this->respondStoreError(
                $request,
                'Admin account could not be created because the database is missing the must_change_password column. Please run migrations and try again.'
            );
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $user = null;

        try {
            DB::transaction(function () use (&$user, $data, $temporaryPassword) {
                $user = User::create([
                    'name'                 => $data['name'],
                    'email'                => $data['email'],
                    'password'             => Hash::make($temporaryPassword),
                    'role'                 => 'admin', // always admin when created by superadmin
                    'email_verified_at'    => now(), // no verification needed for admins
                    'must_change_password' => true,
                ]);

                $this->sendAdminCredentialsEmail($user, $temporaryPassword);

                AuditTrail::record(
                    Auth::id(),
                    AuditDictionary::CREATED_ADMIN_USER,
                    AuditDictionary::MODULE_USERS,
                    "created admin user {$user->name} ({$user->email})"
                );
            });
        } catch (QueryException $e) {
            Log::warning('Admin account creation failed due to database error.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return $this->respondStoreError(
                $request,
                'Admin account could not be created due to a database error. Please run migrations and try again.'
            );
        } catch (TransportExceptionInterface $e) {
            Log::warning('Admin account email failed to send.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            $deliveryError = $this->classifyEmailDeliveryFailure($e->getMessage());

            return $this->respondStoreError(
                $request,
                $deliveryError['message'],
                $deliveryError['status'],
                $deliveryError['code']
            );
        } catch (ViewException $e) {
            Log::warning('Admin account email failed to render/send.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return $this->respondStoreError(
                $request,
                'Email failed to send. Please check the mail configuration and try again.',
                500,
                'email_send_failed'
            );
        } catch (\Throwable $e) {
            Log::warning('Admin account creation failed.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return $this->respondStoreError(
                $request,
                'Admin account could not be created. Please try again.'
            );
        }

        if (!$user) {
            return $this->respondStoreError(
                $request,
                'Admin account could not be created. Please try again.'
            );
        }

        $perPage = 10;
        $position = User::where('role', '!=', 'superadmin')
            ->where(function ($query) use ($user) {
                $query->where('name', '<', $user->name)
                    ->orWhere(function ($subQuery) use ($user) {
                        $subQuery->where('name', '=', $user->name)
                            ->where('id', '<', $user->id);
                    });
            })
            ->count();
        $page = intdiv($position, $perPage) + 1;
        $redirectUrl = route('superadmin.users', ['page' => $page]);
        $successMessage = 'Admin account created. Temporary credentials were sent by email.';

        if ($request->expectsJson()) {
            $request->session()->flash('success', $successMessage);

            return response()->json([
                'message' => $successMessage,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', $successMessage);
    }

    public function checkEmailRealtime(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'max:255', 'email:rfc,dns', 'unique:users,email'],
        ], [
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must be 255 characters or fewer.',
            'email.unique' => 'This email address is already in use.',
        ]);

        $verification = $this->verifyEmailMailboxRealtime($data['email']);
        if (!$verification['ok']) {
            return response()->json([
                'message' => $verification['message'],
                'error_code' => $verification['error_code'],
            ], 422);
        }

        return response()->json([
            'message' => 'Email account verified.',
        ]);
    }

    private function verifyEmailMailboxRealtime(string $email): array
    {
        [$localPart, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($localPart === '' || $domain === '') {
            return [
                'ok' => false,
                'message' => 'Email address/account could not be found. Please verify the email address and try again.',
                'error_code' => 'email_not_found',
            ];
        }

        $mxHosts = [];
        $mxWeights = [];

        if (function_exists('getmxrr') && getmxrr($domain, $mxHosts, $mxWeights) && count($mxHosts) > 0) {
            if (count($mxWeights) !== count($mxHosts)) {
                $mxWeights = array_pad($mxWeights, count($mxHosts), 0);
            }
            array_multisort($mxWeights, SORT_ASC, $mxHosts);
        } elseif (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA')) {
            $mxHosts = [$domain];
        } else {
            return [
                'ok' => false,
                'message' => 'Email address/account could not be found. Please verify the email address and try again.',
                'error_code' => 'email_not_found',
            ];
        }

        $mxHosts = array_slice(array_values(array_unique(array_filter($mxHosts))), 0, 2);
        if (empty($mxHosts)) {
            return [
                'ok' => false,
                'message' => 'Email address/account could not be found. Please verify the email address and try again.',
                'error_code' => 'email_not_found',
            ];
        }

        $heloHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (!is_string($heloHost) || $heloHost === '') {
            $heloHost = request()->getHost() ?: 'localhost';
        }
        $envelopeFrom = $this->resolveVerificationEnvelopeFrom($domain);

        foreach ($mxHosts as $mxHost) {
            $socket = @fsockopen($mxHost, 25, $errno, $errstr, 3);
            if (!$socket) {
                continue;
            }

            stream_set_timeout($socket, 3);

            $greeting = $this->smtpReadResponse($socket);
            if (!$this->smtpResponseHasCode($greeting, [220])) {
                fclose($socket);
                continue;
            }

            $ehlo = $this->smtpSendCommand($socket, "EHLO {$heloHost}");
            if (!$this->smtpResponseHasCode($ehlo, [250])) {
                $helo = $this->smtpSendCommand($socket, "HELO {$heloHost}");
                if (!$this->smtpResponseHasCode($helo, [250])) {
                    $this->smtpSendCommand($socket, 'QUIT');
                    fclose($socket);
                    continue;
                }
            }

            $mailFrom = $this->smtpSendCommand($socket, "MAIL FROM:<{$envelopeFrom}>");
            if (!$this->smtpResponseHasCode($mailFrom, [250])) {
                $this->smtpSendCommand($socket, 'QUIT');
                fclose($socket);
                continue;
            }

            $targetRcpt = $this->smtpSendCommand($socket, "RCPT TO:<{$email}>");
            $targetRcptCode = $this->smtpResponseCode($targetRcpt);

            if (in_array($targetRcptCode, [550, 551, 552, 553, 554], true)) {
                $this->smtpSendCommand($socket, 'RSET');
                $this->smtpSendCommand($socket, 'QUIT');
                fclose($socket);

                return [
                    'ok' => false,
                    'message' => 'Email address/account could not be found. Please verify the email address and try again.',
                    'error_code' => 'email_not_found',
                ];
            }

            if (in_array($targetRcptCode, [250, 251], true)) {
                try {
                    $probeLocalPart = 'probe-' . bin2hex(random_bytes(6));
                } catch (\Throwable) {
                    $probeLocalPart = 'probe-' . uniqid('', true);
                }
                $probeEmail = "{$probeLocalPart}@{$domain}";
                $probeRcpt = $this->smtpSendCommand($socket, "RCPT TO:<{$probeEmail}>");
                $probeRcptCode = $this->smtpResponseCode($probeRcpt);

                $this->smtpSendCommand($socket, 'RSET');
                $this->smtpSendCommand($socket, 'QUIT');
                fclose($socket);

                if (in_array($probeRcptCode, [250, 251], true)) {
                    return [
                        'ok' => false,
                        'message' => 'Could not verify this email account in real time. Please use an address you can confirm and try again.',
                        'error_code' => 'email_check_unavailable',
                    ];
                }

                return [
                    'ok' => true,
                    'message' => 'Email account verified.',
                    'error_code' => null,
                ];
            }

            $this->smtpSendCommand($socket, 'RSET');
            $this->smtpSendCommand($socket, 'QUIT');
            fclose($socket);
        }

        return [
            'ok' => false,
            'message' => 'Could not verify this email account in real time. Please try again.',
            'error_code' => 'email_check_unavailable',
        ];
    }

    private function resolveVerificationEnvelopeFrom(string $fallbackDomain): string
    {
        $configuredFrom = config('mail.from.address');
        if (is_string($configuredFrom) && str_contains($configuredFrom, '@')) {
            return $configuredFrom;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (is_string($appHost) && $appHost !== '') {
            return "no-reply@{$appHost}";
        }

        return "no-reply@{$fallbackDomain}";
    }

    private function smtpSendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->smtpReadResponse($socket);
    }

    private function smtpReadResponse($socket): string
    {
        $response = '';

        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }

        return trim($response);
    }

    private function smtpResponseCode(string $response): ?int
    {
        if (preg_match('/^(\d{3})/m', $response, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function smtpResponseHasCode(string $response, array $expectedCodes): bool
    {
        $code = $this->smtpResponseCode($response);
        return $code !== null && in_array($code, $expectedCodes, true);
    }

    private function respondStoreError(Request $request, string $message, int $status = 500, ?string $errorCode = null): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            $payload = [
                'message' => $message,
            ];

            if ($errorCode) {
                $payload['error_code'] = $errorCode;
            }

            return response()->json($payload, $status);
        }

        $response = back()
            ->withInput()
            ->with('error', $message);

        if ($errorCode) {
            $response->with('error_code', $errorCode);
        }

        return $response;
    }

    private function classifyEmailDeliveryFailure(string $errorMessage): array
    {
        $normalizedError = strtolower($errorMessage);
        $emailNotFoundIndicators = [
            'user unknown',
            'unknown user',
            'no such user',
            'mailbox unavailable',
            'mailbox not found',
            'recipient address rejected',
            'recipient not found',
            'unknown recipient',
            'invalid recipient',
            '5.1.1',
            '550',
        ];

        foreach ($emailNotFoundIndicators as $indicator) {
            if (str_contains($normalizedError, $indicator)) {
                return [
                    'message' => 'Email address/account could not be found. Please verify the email address and try again.',
                    'status' => 422,
                    'code' => 'email_not_found',
                ];
            }
        }

        return [
            'message' => 'Email failed to send. Please check the mail configuration and try again.',
            'status' => 500,
            'code' => 'email_send_failed',
        ];
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'admin') {
            return back()->with('error', 'Only admin accounts can be edited.');
        }

        $data = $request->validate([
            'name'  => ['required','string','max:255'],
        ]);

        $user->update([
            'name' => $data['name'],
        ]);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_ADMIN_USER,
            AuditDictionary::MODULE_USERS,
            "updated admin user {$user->name} ({$user->email})"
        );

        return redirect()->route('superadmin.users')->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::DELETED_USER,
            AuditDictionary::MODULE_USERS,
            "deleted user {$user->name} ({$user->email})"
        );

        return back()->with('success', 'User deleted successfully.');
    }

    public function audit(User $user): View
    {
        if (Auth::check()) {
            AuditTrail::record(
                Auth::id(),
                AuditDictionary::VIEWED_USER_AUDIT_TRAIL,
                AuditDictionary::MODULE_USERS,
                "viewed audit trail for {$user->name} ({$user->email})"
            );
        }

        $audits = AuditTrail::where('user_id', $user->id)->latest()->get();
        return view('superadmin.audit', compact('user','audits'));
    }

    public function recentAudits()
    {
        $audits = AuditTrail::with('user')->latest()->take(50)->get();
        return response()->json($audits);
    }
    public function recentNotifications()
    {
        $notificationService = new NotificationService();
        $user = Auth::user();

        // Get unique notifications for the user
        $notifications = $notificationService->getNotificationsForUser($user, 20);

        return response()->json($notifications);
    }

    public function markAllNotificationsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        Notification::where('user_id', $user->id)->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    public function setNotificationRead(Request $request, Notification $notification)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $data = $request->validate([
            'read' => ['required', 'boolean'],
        ]);

        $notification->update(['read' => $data['read']]);

        return response()->json(['success' => true, 'read' => $notification->read]);
    }

    private function generateTemporaryPassword(int $length = 12): string
    {
        $length = max(8, $length);
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $special = '!@#$%&*?';
        $all = $upper . $lower . $numbers . $special;

        $password = $upper[random_int(0, strlen($upper) - 1)]
            . $lower[random_int(0, strlen($lower) - 1)]
            . $numbers[random_int(0, strlen($numbers) - 1)]
            . $special[random_int(0, strlen($special) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    private function sendAdminCredentialsEmail(User $user, string $temporaryPassword): void
    {
        $mailData = [
            'app_name' => config('app.name', 'Smart Cafeteria'),
            'user_name' => $user->name,
            'user_email' => $user->email,
            'temporary_password' => $temporaryPassword,
            'login_url' => route('login'),
            'created_at' => now()->format('M d, Y h:i A'),
        ];

        Mail::send(
            ['html' => 'emails.admin_account_created', 'text' => 'emails.admin_account_created_plain'],
            $mailData,
            function ($message) use ($user, $mailData) {
                $message->to($user->email, $user->name)
                    ->subject('Your admin account for ' . ($mailData['app_name'] ?? config('app.name')));
            }
        );
    }
}
