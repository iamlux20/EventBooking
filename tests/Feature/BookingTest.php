<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_ticket()
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'quantity' => 100,
            'price' => 50.00,
        ]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 2,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'quantity', 'status']);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'status' => BookingStatus::PENDING->value,
        ]);
    }

    public function test_organizer_cannot_book_ticket()
    {
        $organizer = User::factory()->create(['role' => UserRole::ORGANIZER->value]);
        $ticket = Ticket::factory()->create();
        $token = $organizer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_customer_can_view_bookings()
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        Booking::factory(3)->create(['user_id' => $customer->id]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_customer_can_cancel_booking()
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'status' => BookingStatus::CONFIRMED->value,
        ]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::CANCELLED->value,
        ]);
    }

    public function test_customer_cannot_double_book_same_ticket()
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $ticket = Ticket::factory()->create();
        Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'status' => BookingStatus::PENDING->value,
        ]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 1,
        ]);

        $response->assertStatus(409)
            ->assertJson(['message' => 'You already have an active booking for this ticket']);
    }
}
