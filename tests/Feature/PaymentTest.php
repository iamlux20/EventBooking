<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_make_payment()
    {
        // Mock the payment service to return success
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processPayment')->once()->andReturn(true);
        });

        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $ticket = Ticket::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'status' => BookingStatus::PENDING->value,
        ]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/bookings/{$booking->id}/payment", [
            'amount' => 100.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'amount', 'status', 'booking_id']);
    }

    public function test_can_view_payment()
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $booking = Booking::factory()->create(['user_id' => $customer->id]);
        $payment = Payment::factory()->create(['booking_id' => $booking->id]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'amount', 'status', 'booking_id']);
    }

    public function test_successful_payment_updates_booking_status()
    {
        // Mock the payment service to return success
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processPayment')->once()->andReturn(true);
        });

        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $ticket = Ticket::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'status' => BookingStatus::PENDING->value,
        ]);
        $token = $customer->createToken('test')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/bookings/{$booking->id}/payment", [
            'amount' => 100.00,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::CONFIRMED->value,
        ]);
    }

    public function test_organizer_cannot_make_payment()
    {
        $organizer = User::factory()->create(['role' => UserRole::ORGANIZER->value]);
        $booking = Booking::factory()->create();
        $token = $organizer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/bookings/{$booking->id}/payment", [
            'amount' => 100.00,
        ]);

        $response->assertStatus(403);
    }

    // Optional: Test for failed payment scenario
    public function test_failed_payment_does_not_update_booking()
    {
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processPayment')->once()->andReturn(false);
        });

        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $ticket = Ticket::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'status' => BookingStatus::PENDING->value,
        ]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/bookings/{$booking->id}/payment", [
            'amount' => 100.00,
        ]);

        $response->assertStatus(418);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::PENDING->value,
        ]);
    }
}
