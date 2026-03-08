<?php

use App\Mail\StandardAppMail;
use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Services\RealtimeEmailVerifier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

function verifierThatFailsIfMailboxProbeRuns(): RealtimeEmailVerifier
{
    return new class extends RealtimeEmailVerifier {
        public function verifyMailbox(string $email): array
        {
            throw new RuntimeException('SMTP mailbox probe should not run for add-admin flow.');
        }
    };
}

test('superadmin add-admin creation is not blocked by realtime mailbox probe failures', function () {
    Mail::fake();
    Notification::fake();

    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    $this->instance(RealtimeEmailVerifier::class, verifierThatFailsIfMailboxProbeRuns());

    $response = $this->actingAs($superAdmin)->postJson(route('superadmin.users.store'), [
        'name' => 'Pending Admin',
        'email' => 'pending-admin@example.com',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['message', 'redirect_url'])
        ->assertJsonPath('message', 'Admin account created. Temporary credentials were sent by email. The email owner must confirm the account through the verification email before the account becomes active.');

    $pendingAdmin = User::where('email', 'pending-admin@example.com')->firstOrFail();

    $this->assertSame('admin_pending', $pendingAdmin->role);
    $this->assertNull($pendingAdmin->email_verified_at);
    $this->assertTrue((bool) $pendingAdmin->must_change_password);

    Mail::assertSent(StandardAppMail::class, function (StandardAppMail $mail) {
        return $mail->hasTo('pending-admin@example.com');
    });

    Notification::assertSentTo($pendingAdmin, VerifyEmail::class);
    Notification::assertSentToTimes($pendingAdmin, VerifyEmail::class, 1);
});

test('superadmin add-admin reuses existing pending email records instead of creating duplicates', function () {
    Mail::fake();
    Notification::fake();

    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    $existingPending = User::factory()->create([
        'name' => 'Old Pending User',
        'email' => 'shared-pending@example.com',
        'role' => 'customer_pending',
        'email_verified_at' => null,
        'must_change_password' => false,
    ]);

    $response = $this->actingAs($superAdmin)->postJson(route('superadmin.users.store'), [
        'name' => 'Updated Pending Admin',
        'email' => 'shared-pending@example.com',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Admin account created. Temporary credentials were sent by email. The email owner must confirm the account through the verification email before the account becomes active.');

    expect(User::where('email', 'shared-pending@example.com')->count())->toBe(1);

    $reusedPending = User::where('email', 'shared-pending@example.com')->firstOrFail();
    expect($reusedPending->id)->toBe($existingPending->id);
    expect($reusedPending->name)->toBe('Updated Pending Admin');
    expect($reusedPending->role)->toBe('admin_pending');
    expect($reusedPending->email_verified_at)->toBeNull();
    expect((bool) $reusedPending->must_change_password)->toBeTrue();
});

test('superadmin add-admin realtime email check validates format and uniqueness without mailbox probing', function () {
    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    $this->instance(RealtimeEmailVerifier::class, verifierThatFailsIfMailboxProbeRuns());

    User::factory()->create([
        'email' => 'existing-admin@example.com',
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    User::factory()->create([
        'email' => 'pending-admin@example.com',
        'role' => 'admin_pending',
        'email_verified_at' => null,
    ]);

    $okResponse = $this->actingAs($superAdmin)->postJson(route('superadmin.users.check-email'), [
        'email' => 'new-admin@example.com',
    ]);

    $okResponse->assertOk()
        ->assertJsonPath('message', 'Email address is valid and available.');

    $pendingResponse = $this->actingAs($superAdmin)->postJson(route('superadmin.users.check-email'), [
        'email' => 'pending-admin@example.com',
    ]);

    $pendingResponse->assertOk()
        ->assertJsonPath('message', 'Email address is valid and available.');

    $duplicateResponse = $this->actingAs($superAdmin)->postJson(route('superadmin.users.check-email'), [
        'email' => 'existing-admin@example.com',
    ]);

    $duplicateResponse->assertStatus(422)
        ->assertJsonPath('errors.email.0', 'This email address is already in use.');
});

test('pending customer accounts appear in superadmin users list with pending status', function () {
    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    User::factory()->create([
        'name' => 'Visible Customer',
        'email' => 'visible-customer@example.com',
        'role' => 'customer',
    ]);

    User::factory()->create([
        'name' => 'Hidden Pending Customer',
        'email' => 'hidden-pending-customer@example.com',
        'role' => 'customer_pending',
        'email_verified_at' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeText('visible-customer@example.com')
        ->assertSeeText('hidden-pending-customer@example.com')
        ->assertSeeInOrder(['hidden-pending-customer@example.com', 'Pending']);
});

test('verified pending customers stay pending in the users list until their first successful login', function () {
    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    $pendingCustomer = User::factory()->create([
        'name' => 'Pending Customer User',
        'email' => 'pending-customer-activation@example.com',
        'role' => 'customer_pending',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeText('pending-customer-activation@example.com')
        ->assertSeeInOrder(['pending-customer-activation@example.com', 'Pending']);

    $this->actingAs($superAdmin)->post(route('logout'));

    $this->post(route('login'), [
        'email' => $pendingCustomer->email,
        'password' => 'password',
    ])->assertRedirect(route('customer.homepage', absolute: false));

    expect($pendingCustomer->refresh()->role)->toBe('customer');

    $this->post(route('logout'));

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeText('pending-customer-activation@example.com')
        ->assertSeeInOrder(['pending-customer-activation@example.com', 'Active']);
});

test('newly created admins appear as pending until email verification and first successful login activate them', function () {
    Mail::fake();
    Notification::fake();

    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    $this->actingAs($superAdmin)->postJson(route('superadmin.users.store'), [
        'name' => 'Hidden Admin',
        'email' => 'hidden-admin@example.com',
    ])->assertOk();

    $pendingAdmin = User::where('email', 'hidden-admin@example.com')->firstOrFail();

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeText('hidden-admin@example.com')
        ->assertSeeInOrder(['hidden-admin@example.com', 'Pending']);

    $verificationLink = URL::signedRoute('verification.link', [
        'id' => $pendingAdmin->id,
        'hash' => sha1($pendingAdmin->getEmailForVerification()),
    ]);

    $this->get($verificationLink)->assertRedirect(route('login', absolute: false));

    $pendingAdmin->refresh();
    expect($pendingAdmin->email_verified_at)->not->toBeNull();

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeText('hidden-admin@example.com')
        ->assertSeeInOrder(['hidden-admin@example.com', 'Pending']);

    $pendingAdmin->forceFill([
        'password' => Hash::make('Password1!'),
    ])->save();

    $this->actingAs($superAdmin)->post(route('logout'));

    $this->post(route('login'), [
        'email' => 'hidden-admin@example.com',
        'password' => 'Password1!',
    ])->assertRedirect(route('admin.dashboard', absolute: false));

    $pendingAdmin->refresh();
    $this->assertSame('admin', $pendingAdmin->role);

    $this->post(route('logout'));

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeText('hidden-admin@example.com')
        ->assertSeeInOrder(['hidden-admin@example.com', 'Active']);
});

test('superadmin users list sorts by created date newest first by default and can be reversed', function () {
    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    User::factory()->create([
        'name' => 'Older User',
        'email' => 'older-user@example.com',
        'created_at' => now()->subDays(2),
    ]);

    User::factory()->create([
        'name' => 'Newest User',
        'email' => 'newest-user@example.com',
        'created_at' => now()->subHour(),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSeeInOrder(['newest-user@example.com', 'older-user@example.com']);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users', ['created_sort' => 'asc']))
        ->assertOk()
        ->assertSeeInOrder(['older-user@example.com', 'newest-user@example.com']);
});

test('superadmin user management filters and search across the entire dataset instead of only the current page', function () {
    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    User::factory()->count(10)->sequence(
        fn ($sequence) => [
            'name' => 'Top Admin '.($sequence->index + 1),
            'email' => 'top-admin-'.($sequence->index + 1).'@example.com',
            'role' => 'admin',
            'created_at' => now()->subMinutes($sequence->index),
        ],
    )->create();

    User::factory()->create([
        'name' => 'Dataset Customer Match',
        'email' => 'dataset-customer-match@example.com',
        'role' => 'customer',
        'created_at' => now()->subDays(2),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertDontSeeText('dataset-customer-match@example.com');

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users', ['role' => 'customer']))
        ->assertOk()
        ->assertSeeText('dataset-customer-match@example.com')
        ->assertDontSeeText('top-admin-1@example.com');

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users', ['search' => 'Dataset Customer Match']))
        ->assertOk()
        ->assertSeeText('dataset-customer-match@example.com')
        ->assertDontSeeText('top-admin-1@example.com');
});

test('superadmin users pagination keeps active search and role filters', function () {
    $superAdmin = User::factory()->create([
        'role' => 'superadmin',
    ]);

    User::factory()->create([
        'name' => 'Pagination Admin',
        'email' => 'pagination-admin@example.com',
        'role' => 'admin',
        'created_at' => now()->addMinute(),
    ]);

    User::factory()->count(12)->sequence(
        fn ($sequence) => [
            'name' => 'Paged Customer '.($sequence->index + 1),
            'email' => 'paged-customer-'.($sequence->index + 1).'@example.com',
            'role' => 'customer',
            'created_at' => now()->subMinutes($sequence->index),
        ],
    )->create();

    $response = $this->actingAs($superAdmin)
        ->get(route('superadmin.users', [
            'search' => 'Paged Customer',
            'role' => 'customer',
            'created_sort' => 'desc',
        ]));

    $response->assertOk()
        ->assertSeeText('paged-customer-1@example.com')
        ->assertSeeText('paged-customer-10@example.com')
        ->assertDontSeeText('paged-customer-11@example.com')
        ->assertDontSeeText('pagination-admin@example.com');

    $pageTwoUrl = null;
    preg_match('/<a href="([^"]+)"[^>]*aria-label="Go to page 2"/', $response->getContent(), $matches);
    if (isset($matches[1])) {
        $pageTwoUrl = html_entity_decode($matches[1], ENT_QUOTES);
    }

    expect($pageTwoUrl)->not->toBeNull();

    parse_str((string) parse_url($pageTwoUrl, PHP_URL_QUERY), $query);

    expect($query)->toMatchArray([
        'search' => 'Paged Customer',
        'role' => 'customer',
        'created_sort' => 'desc',
        'page' => '2',
    ]);

    $pageTwoPath = (string) parse_url($pageTwoUrl, PHP_URL_PATH);
    $pageTwoQuery = (string) parse_url($pageTwoUrl, PHP_URL_QUERY);

    $this->actingAs($superAdmin)
        ->get($pageTwoPath.'?'.$pageTwoQuery)
        ->assertOk()
        ->assertSeeText('paged-customer-11@example.com')
        ->assertSeeText('paged-customer-12@example.com')
        ->assertDontSeeText('pagination-admin@example.com');
});
