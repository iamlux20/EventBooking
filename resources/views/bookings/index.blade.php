@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold mb-6">My Bookings</h2>

    @forelse($bookings as $booking)
        <div class="bg-white p-6 rounded shadow mb-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-xl font-bold">{{ $booking->ticket->event->name }}</h3>
                    <p class="text-gray-600">{{ $booking->ticket->type }} - {{ $booking->ticket->description }}</p>
                    <div class="flex gap-4 text-sm text-gray-500 mt-2">
                        <span>📅 {{ $booking->ticket->event->date->format('M d, Y H:i') }}</span>
                        <span>📍 {{ $booking->ticket->event->location }}</span>
                        <span>🎫 Quantity: {{ $booking->quantity }}</span>
                    </div>
                    <p class="text-lg font-bold mt-2">Total: ${{ number_format($booking->total_amount, 2) }}</p>
                </div>
                
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded text-sm font-bold
                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                    
                    @if($booking->status === 'pending')
                        <div class="mt-2">
                            <a href="{{ route('payments.create', $booking) }}" 
                               class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                Pay Now
                            </a>
                        </div>
                    @endif
                    
                    @if($booking->status !== 'cancelled')
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"
                                    onclick="return confirm('Cancel this booking?')">
                                Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white p-6 rounded shadow text-center">
            <p class="text-gray-500">You haven't made any bookings yet.</p>
            <a href="{{ route('events.index') }}" class="text-blue-500 hover:underline">Browse Events</a>
        </div>
    @endforelse

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>
@endsection