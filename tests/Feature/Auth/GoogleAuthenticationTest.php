<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('pending accounts are blocked from signing in with google', function () {
    User::factory()->create([
        'email' => 'pending-google@example.com',
        'role' => 'customer_pending',
        'email_verified_at' => null,
    ]);

    $googleUser = Mockery::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getEmail')->andReturn('pending-google@example.com');
    $googleUser->shouldReceive('getName')->andReturn('Pending Google User');
    $googleUser->shouldReceive('getId')->andReturn('pending-google-id');

    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login', absolute: false))
        ->assertSessionHasErrors([
            'google' => 'This account already exists and is awaiting verification. Please check your email for the verification link or contact the admin.',
        ]);

    $this->assertGuest();
});

test('new google accounts are created without a local password', function () {
    $googleUser = Mockery::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getEmail')->andReturn('new-google-user@example.com');
    $googleUser->shouldReceive('getName')->andReturn('New Google User');
    $googleUser->shouldReceive('getId')->andReturn('google-user-456');

    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'new-google-user@example.com')->firstOrFail();
    expect($user->google_id)->toBe('google-user-456');
    expect($user->hasLocalPassword())->toBeFalse();
    expect($user->email_verified_at)->not->toBeNull();
});
