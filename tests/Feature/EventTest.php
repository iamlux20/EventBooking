<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_create_event()
    {
        $organizer = User::factory()->create(['role' => UserRole::ORGANIZER->value]);
        $token = $organizer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/events', [
            'title' => 'Test Event',
            'date' => '2024-12-25',
            'location' => 'Test Location',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'date', 'location']);

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'location' => 'Test Location',
        ]);
    }

    public function test_customer_cannot_create_event()
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER->value]);
        $token = $customer->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/events', [
            'title' => 'Test Event',
            'date' => '2024-12-25',
            'location' => 'Test Location',
        ]);

        $response->assertStatus(403);
    }

    public function test_can_view_events()
    {
        Event::factory(3)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_single_event()
    {
        $event = Event::factory()->create();
        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['title', 'date', 'location']);
    }
}
