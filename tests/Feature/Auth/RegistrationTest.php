<?php

use App\Services\RealtimeEmailVerifier;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and are sent to verification flow', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('verification.notice', absolute: false));
    $response->assertSessionHas('registered');
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'customer_pending',
    ]);

    $registeredUser = \App\Models\User::where('email', 'test@example.com')->firstOrFail();
    Notification::assertSentToTimes($registeredUser, VerifyEmail::class, 1);
});

test('registration blocks duplicate sign-ups for pending accounts', function () {
    Notification::fake();

    $pendingUser = \App\Models\User::factory()->create([
        'name' => 'Old Pending Name',
        'email' => 'pending-user@example.com',
        'role' => 'customer_pending',
        'email_verified_at' => null,
        'password' => Hash::make('OldPassword1!'),
    ]);

    $response = $this->from('/register')->post('/register', [
        'name' => 'Updated Pending Name',
        'address' => 'Updated Address',
        'contact_no' => '09999999999',
        'department' => 'Updated Department',
        'email' => 'pending-user@example.com',
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ]);

    $response->assertRedirect('/register')
        ->assertSessionHasErrors([
            'email' => 'This account already exists and is awaiting verification. Please check your email for the verification link or contact the admin.',
        ]);

    expect(\App\Models\User::where('email', 'pending-user@example.com')->count())->toBe(1);

    $reusedUser = \App\Models\User::where('email', 'pending-user@example.com')->firstOrFail();
    expect($reusedUser->id)->toBe($pendingUser->id);
    expect($reusedUser->name)->toBe('Old Pending Name');
    expect($reusedUser->role)->toBe('customer_pending');
    expect($reusedUser->email_verified_at)->toBeNull();
    expect(Hash::check('OldPassword1!', $reusedUser->password))->toBeTrue();

    Notification::assertNothingSent();
});

test('registration still rejects duplicate emails that belong to active accounts', function () {
    \App\Models\User::factory()->create([
        'email' => 'active-user@example.com',
        'role' => 'customer',
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/register', [
        'name' => 'Duplicate User',
        'email' => 'active-user@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('errors.email.0', 'This email is already registered.');
});

test('registration does not block account creation when realtime mailbox checks fail', function () {
    $failedVerification = [
        'ok' => false,
        'message' => 'Could not verify this email account in real time. Please input a valid email and try again.',
        'error_code' => 'email_check_unavailable',
    ];

    $this->instance(RealtimeEmailVerifier::class, new class($failedVerification) extends RealtimeEmailVerifier {
        public function __construct(private array $result)
        {
        }

        public function verifyMailbox(string $email): array
        {
            return $this->result;
        }
    });

    $response = $this->postJson('/register', [
        'name' => 'Ghost User',
        'email' => 'ghost@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('redirect', route('verification.notice'))
        ->assertJsonPath('message', 'Account created successfully! Please check your email to verify your account.');

    $this->assertDatabaseHas('users', [
        'email' => 'ghost@example.com',
        'role' => 'customer_pending',
    ]);
});
