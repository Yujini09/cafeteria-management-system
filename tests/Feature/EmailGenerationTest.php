<?php

use App\Mail\StandardAppMail;
use App\Models\ContactMessage;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('contact form sends the standardized email to admins', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->post('/contact', [
        'name' => 'Jane Customer',
        'email' => 'jane@example.com',
        'message' => 'I would like to ask about reservation availability for next week.',
    ]);

    $response->assertRedirect(route('contact'));

    Mail::assertSent(StandardAppMail::class, function (StandardAppMail $mail) use ($admin) {
        return $mail->hasTo($admin->email)
            && str_contains($mail->render(), 'A new contact message was submitted');
    });
});

test('feedback submission sends the standardized email to admins', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->post('/feedback', [
        'name' => 'Jamie Guest',
        'message' => 'Great service overall and the ordering process was very smooth.',
        'rating' => 5,
    ]);

    $response->assertSessionHas('success');

    Mail::assertSent(StandardAppMail::class, function (StandardAppMail $mail) use ($admin) {
        return $mail->hasTo($admin->email)
            && str_contains($mail->render(), 'A new feedback entry was submitted');
    });
});

test('admin reply sends the standardized message response email', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $contact = ContactMessage::create([
        'name' => 'Pat Customer',
        'email' => 'pat@example.com',
        'message' => 'Could you confirm if outside catering is allowed for reservations?',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.messages.reply', $contact->id), [
            'subject' => 'Reservation Follow-up',
            'message' => 'Outside catering is not allowed, but we can help you with menu options.',
        ]);

    $response->assertOk()->assertJson(['success' => true]);

    Mail::assertSent(StandardAppMail::class, function (StandardAppMail $mail) use ($contact) {
        return $mail->hasTo($contact->email)
            && str_contains($mail->render(), 'We replied to your message');
    });
});

test('reservation email schedule formats times in 12 hour format', function () {
    $reservation = new Reservation([
        'event_date' => '2026-03-03',
        'end_date' => '2026-03-04',
        'day_times' => [
            '2026-03-03' => [
                'start_time' => '13:00',
                'end_time' => '15:30:00',
            ],
            '2026-03-04' => [
                'start_time' => '08:15',
                'end_time' => '12:00',
            ],
        ],
    ]);

    expect($reservation->emailScheduleSummary())->toBe(
        "Mar 3, 2026 at 1:00 PM - 3:30 PM\nMar 4, 2026 at 8:15 AM - 12:00 PM"
    );
});
