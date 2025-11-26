@extends('layouts.app')

@section('title', $event->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded shadow mb-6">
        <h1 class="text-3xl font-bold mb-4">{{ $event->name }}</h1>
        <p class="text-gray-600 mb-4">{{ $event->description }}</p>
        <div class="flex gap-6 text-sm text-gray-500 mb-6">
            <span>📅 {{ $event->date->format('M d, Y H:i') }}</span>
            <span>📍 {{ $event->location }}</span>
            <span>👤 {{ $event->organizer->name ?? 'Unknown' }}</span>
        </div>

        @auth
            @if(auth()->user()->role === 'organizer' && auth()->id() === $event->user_id)
                <div class="flex gap-2 mb-6">
                    <a href="{{ route('events.edit', $event) }}" 
                       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                        Edit Event
                    </a>
                    <a href="{{ route('tickets.create', $event) }}" 
                       class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Add Tickets
                    </a>
                </div>
            @endif
        @endauth
    </div>

    @if($event->tickets->count() > 0)
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Available Tickets</h2>
            <div class="grid gap-4">
                @foreach($event->tickets as $ticket)
                    <div class="border p-4 rounded flex justify-between items-center">
                        <div>
                            <h3 class="font-bold">{{ $ticket->type }}</h3>
                            <p class="text-gray-600">{{ $ticket->description }}</p>
                            <p class="text-sm text-gray-500">Available: {{ $ticket->quantity - $ticket->bookings_count }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold">${{ number_format($ticket->price, 2) }}</p>
                            @auth
                                @if(auth()->user()->role === 'customer' && $ticket->quantity > $ticket->bookings_count)
                                    <form method="POST" action="{{ route('bookings.store', $ticket) }}" class="mt-2">
                                        @csrf
                                        <input type="number" name="quantity" min="1" max="{{ $ticket->quantity - $ticket->bookings_count }}" 
                                               value="1" class="w-16 p-1 border rounded text-center">
                                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded ml-2 hover:bg-blue-600">
                                            Book
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded shadow text-center">
            <p class="text-gray-500">No tickets available for this event yet.</p>
        </div>
    @endif
</div>
@endsection