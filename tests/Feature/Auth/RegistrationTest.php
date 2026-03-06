<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and are sent to verification flow', function () {
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
        'role' => 'customer',
    ]);
});
