<?php

namespace App\Http\Controllers;

use App\Mail\StandardAppMail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditTrail;
use App\Models\Notification;
use App\Services\RealtimeEmailVerifier;
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
    private const ROLE_PENDING_CUSTOMER = 'customer_pending';
    private const ROLE_PENDING_ADMIN = 'admin_pending';

    public function __construct(
        private readonly RealtimeEmailVerifier $realtimeEmailVerifier
    ) {
    }

    public function index(): View
    {
        // Show everyone except superadmin and pending accounts.
        $users = User::where('role', '!=', 'superadmin')
            ->where('role', '!=', self::ROLE_PENDING_CUSTOMER)
            ->where('role', '!=', self::ROLE_PENDING_ADMIN)
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $emailRule = app()->runningUnitTests() ? 'email' : 'email:rfc,dns';

        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => [
                'required',
                'string',
                'lowercase',
                'max:255',
                $emailRule,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $email = is_string($value) ? $value : '';
                    if ($this->hasActiveAccountForEmail($email)) {
                        $fail('This email address is already in use.');
                    }
                },
            ],
        ], [
            'name.required' => 'Please enter the full name.',
            'name.max' => 'Full name must be 255 characters or fewer.',
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must be 255 characters or fewer.',
            'email.unique' => 'This email address is already in use.',
        ]);

        if (!Schema::hasColumn('users', 'must_change_password')) {
            return $this->respondStoreError(
                $request,
                'Admin account could not be created because the database is missing the must_change_password column. Please run migrations and try again.'
            );
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $user = null;
        $reusedPendingAccount = false;

        try {
            DB::transaction(function () use (&$user, &$reusedPendingAccount, $data, $temporaryPassword) {
                $pendingAccount = $this->findPendingAccountByEmail($data['email'], true);
                $reusedPendingAccount = $pendingAccount !== null;

                if ($pendingAccount) {
                    $pendingAccount->name = $data['name'];
                    $pendingAccount->email = $data['email'];
                    $pendingAccount->password = Hash::make($temporaryPassword);
                    $pendingAccount->role = self::ROLE_PENDING_ADMIN;
                    $pendingAccount->email_verified_at = null;
                    $pendingAccount->must_change_password = true;
                    $pendingAccount->save();

                    $user = $pendingAccount;
                } else {
                    $user = User::create([
                        'name'                 => $data['name'],
                        'email'                => $data['email'],
                        'password'             => Hash::make($temporaryPassword),
                        'role'                 => self::ROLE_PENDING_ADMIN,
                        'email_verified_at'    => null,
                        'must_change_password' => true,
                    ]);
                }

                $this->sendAdminCredentialsEmail($user, $temporaryPassword);
                $user->sendEmailVerificationNotification();

                AuditTrail::record(
                    Auth::id(),
                    AuditDictionary::CREATED_ADMIN_USER,
                    AuditDictionary::MODULE_USERS,
                    $reusedPendingAccount
                        ? "updated pending admin user {$user->name} ({$user->email})"
                        : "created pending admin user {$user->name} ({$user->email})"
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
            ->where('role', '!=', self::ROLE_PENDING_CUSTOMER)
            ->where('role', '!=', self::ROLE_PENDING_ADMIN)
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
        $successMessage = 'Admin account created. Temporary credentials were sent by email. The email owner must confirm the account through the verification email before the account becomes active.';

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
        $emailRule = app()->runningUnitTests() ? 'email' : 'email:rfc,dns';

        $request->validate([
            'email' => [
                'required',
                'string',
                'lowercase',
                'max:255',
                $emailRule,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $email = is_string($value) ? $value : '';
                    if ($this->hasActiveAccountForEmail($email)) {
                        $fail('This email address is already in use.');
                    }
                },
            ],
        ], [
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must be 255 characters or fewer.',
            'email.unique' => 'This email address is already in use.',
        ]);

        return response()->json([
            'message' => 'Email address is valid and available.',
        ]);
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

        $isEmailFieldError = in_array($errorCode, ['email_not_found'], true);

        $response = back()->withInput();

        if ($isEmailFieldError) {
            $response->withErrors([
                'email' => $message,
            ]);
        } else {
            $response->with('error', $message);
        }

        if ($errorCode) {
            $response->with('error_code', $errorCode);
        }

        return $response;
    }

    private function classifyEmailDeliveryFailure(string $errorMessage): array
    {
        return $this->realtimeEmailVerifier->classifyDeliveryFailure($errorMessage);
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

    private function hasActiveAccountForEmail(string $email): bool
    {
        $normalizedEmail = mb_strtolower(trim($email));
        if ($normalizedEmail === '') {
            return false;
        }

        return User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->whereNotIn('role', [self::ROLE_PENDING_CUSTOMER, self::ROLE_PENDING_ADMIN])
            ->exists();
    }

    private function findPendingAccountByEmail(string $email, bool $forUpdate = false): ?User
    {
        $normalizedEmail = mb_strtolower(trim($email));
        if ($normalizedEmail === '') {
            return null;
        }

        $query = User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->whereIn('role', [self::ROLE_PENDING_CUSTOMER, self::ROLE_PENDING_ADMIN]);

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function sendAdminCredentialsEmail(User $user, string $temporaryPassword): void
    {
        Mail::to($user->email, $user->name)->send(
            new StandardAppMail(
                topic: 'Admin Account Details',
                title: 'Your admin account is ready',
                recipientName: $user->name,
                introLines: [
                    'An admin account has been created for you.',
                    'Before this account becomes active, confirm your email address using the verification email sent separately.',
                    'After verification, use the temporary password below to sign in, then change it immediately.',
                ],
                details: [
                    'Email Address' => $user->email,
                    'Temporary Password' => $temporaryPassword,
                    'Created At' => now()->format('M d, Y h:i A'),
                ],
                action: [
                    'text' => 'Sign In',
                    'url' => route('login'),
                ],
                outroLines: [
                    'For security, you will be required to change this password after you sign in.',
                    'If you did not expect this email, contact support immediately.',
                ],
                headerLabel: 'Admin Access',
            )
        );
    }
}
