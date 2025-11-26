<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(2)->create([
            'role' => UserRole::ORGANIZER->value,
        ]);

        User::factory(3)->create([
            'role' => UserRole::ORGANIZER->value,
        ]);

        User::factory(10)->create([
            'role' => UserRole::CUSTOMER->value,
        ]);

        // Create additional users
        User::factory(10)->create();

        // Create events with tickets
        Event::factory(5)->create()->each(function ($event) {
            Ticket::factory(15)->create(['event_id' => $event->id]);
        });


        // Create bookings with payments
        Booking::factory(20)->create()->each(function ($booking) {
            if ($booking->status === BookingStatus::CONFIRMED->value) {
                Payment::factory()->create([
                    'booking_id' => $booking->id,
                    'status' => PaymentStatus::SUCCESS->value,
                ]);
            }
        });
    }
}
