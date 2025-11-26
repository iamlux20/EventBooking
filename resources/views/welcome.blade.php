@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="text-center mb-12">
    <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to Event Booking</h1>
    <p class="text-xl text-gray-600">Discover and book amazing events</p>
</div>

@guest
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow text-center">
        <h2 class="text-2xl font-bold mb-4">Get Started</h2>
        <p class="text-gray-600 mb-6">Join our platform to book events or create your own as an organizer</p>
        <div class="flex gap-4 justify-center">
            <a href="{{ route('register') }}" class="bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600">
                Sign Up
            </a>
            <a href="{{ route('login') }}" class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600">
                Login
            </a>
        </div>
    </div>
@else
    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-bold mb-4">Browse Events</h3>
            <p class="text-gray-600 mb-4">Discover exciting events happening near you</p>
            <a href="{{ route('events.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                View Events
            </a>
        </div>

        @if(auth()->user()->role === 'customer')
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-xl font-bold mb-4">My Bookings</h3>
                <p class="text-gray-600 mb-4">Manage your event bookings and tickets</p>
                <a href="{{ route('bookings.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    View Bookings
                </a>
            </div>
        @endif

        @if(auth()->user()->role === 'organizer')
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-xl font-bold mb-4">Create Event</h3>
                <p class="text-gray-600 mb-4">Start organizing your next amazing event</p>
                <a href="{{ route('events.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Create Event
                </a>
            </div>
        @endif
    </div>
@endguest

@if(isset($featuredEvents) && $featuredEvents->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Featured Events</h2>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($featuredEvents as $event)
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="font-bold">{{ $event->name }}</h3>
                    <p class="text-sm text-gray-600">{{ Str::limit($event->description, 80) }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $event->date->format('M d, Y') }}</p>
                    <a href="{{ route('events.show', $event) }}" class="text-blue-500 text-sm hover:underline">
                        Learn More
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection