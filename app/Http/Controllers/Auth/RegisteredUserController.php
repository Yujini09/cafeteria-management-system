<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\RealtimeEmailVerifier;
use App\Support\PasswordRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\View\ViewException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisteredUserController extends Controller
{
    private const ROLE_PENDING_CUSTOMER = 'customer_pending';
    private const ROLE_PENDING_ADMIN = 'admin_pending';
    private const PENDING_ACCOUNT_MESSAGE = 'This account already exists and is awaiting verification. Please check your email for the verification link or contact the admin.';

    public function __construct(
        private readonly RealtimeEmailVerifier $realtimeEmailVerifier
    ) {
    }

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        (new NotificationService())->createAdminNotification($action, $module, $description, $metadata);
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $emailRule = app()->runningUnitTests() ? 'email' : 'email:rfc,dns';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                $emailRule,
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $email = is_string($value) ? $value : '';
                    if ($this->hasActiveAccountForEmail($email)) {
                        $fail('This email is already registered.');
                    }
                },
            ],
            'password' => PasswordRules::validationRules(true),
        ], [
            'email.required' => 'Please input a valid email.',
            'email.email' => 'Please input a valid email.',
        ]);

        if ($this->findPendingAccountByEmail($data['email'])) {
            return $this->respondRegistrationError(
                $request,
                self::PENDING_ACCOUNT_MESSAGE,
                422,
                'pending_account'
            );
        }

        $payload = [
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'address'    => $data['address'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,
            'department' => $data['department'] ?? null,
            'role'       => self::ROLE_PENDING_CUSTOMER,
        ];

        if (Schema::hasColumn('users', 'must_change_password')) {
            $payload['must_change_password'] = false;
        }

        $user = User::create($payload);
        $createdNewAccount = true;

        try {
            // Send email verification notification
            $user->sendEmailVerificationNotification();
        } catch (TransportExceptionInterface $e) {
            Log::warning('Customer registration email failed to send.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            $deliveryError = $this->realtimeEmailVerifier->classifyDeliveryFailure($e->getMessage());
            if ($createdNewAccount) {
                $user->delete();
            }

            return $this->respondRegistrationError(
                $request,
                $deliveryError['message'],
                $deliveryError['status'],
                $deliveryError['code']
            );
        } catch (ViewException $e) {
            Log::warning('Customer verification email failed to render/send.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            if ($createdNewAccount) {
                $user->delete();
            }

            return $this->respondRegistrationError(
                $request,
                'Email failed to send. Please check the mail configuration and try again.',
                500,
                'email_send_failed'
            );
        }

        // Store user ID in session for manual verification
        session()->put('verification_user_id', $user->id);

        // Create notification for admins/superadmin about new user registration
        $this->createAdminNotification('user_registered', 'users', "New customer {$user->name} has registered", [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        // Return JSON response for AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully! Please check your email to verify your account.',
                'redirect' => route('verification.notice')
            ]);
        }

        // Flash success message for verification notice
        session()->flash('registered', 'Account created successfully! Please check your email to verify your account.');

        return redirect()->route('verification.notice');
    }

    private function respondRegistrationError(
        Request $request,
        string $message,
        int $status = 422,
        ?string $errorCode = null
    ): RedirectResponse|JsonResponse {
        if ($request->expectsJson()) {
            $payload = [
                'success' => false,
                'message' => $message,
                'errors' => [
                    'email' => [$message],
                ],
            ];

            if ($errorCode) {
                $payload['error_code'] = $errorCode;
            }

            return response()->json($payload, $status);
        }

        $response = back()
            ->withInput($request->except(['password', 'password_confirmation']))
            ->withErrors([
                'email' => $message,
            ]);

        if ($errorCode) {
            $response->with('error_code', $errorCode);
        }

        return $response;
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

    private function findPendingAccountByEmail(string $email): ?User
    {
        $normalizedEmail = mb_strtolower(trim($email));
        if ($normalizedEmail === '') {
            return null;
        }

        return User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->whereIn('role', [self::ROLE_PENDING_CUSTOMER, self::ROLE_PENDING_ADMIN])
            ->first();
    }
}
