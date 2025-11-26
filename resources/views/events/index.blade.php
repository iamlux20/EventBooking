@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Events</h2>
    @auth
        @if(auth()->user()->role === 'organizer')
            <a href="{{ route('events.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Event
            </a>
        @endif
    @endauth
</div>

<div class="bg-white p-4 rounded shadow mb-6">
    <form method="GET" action="{{ route('events.index') }}">
        <div class="flex gap-4">
            <input type="text" name="search" placeholder="Search events..." 
                   class="flex-1 p-2 border rounded" value="{{ request('search') }}">
            <input type="date" name="date" class="p-2 border rounded" value="{{ request('date') }}">
            <input type="text" name="location" placeholder="Location" 
                   class="p-2 border rounded" value="{{ request('location') }}">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
        </div>
    </form>
</div>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse($events as $event)
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-bold mb-2">{{ $event->name }}</h3>
            <p class="text-gray-600 mb-4">{{ Str::limit($event->description, 100) }}</p>
            <p class="text-sm text-gray-500 mb-2">📅 {{ $event->date->format('M d, Y H:i') }}</p>
            <p class="text-sm text-gray-500 mb-4">📍 {{ $event->location }}</p>
            
            <div class="flex gap-2">
                <a href="{{ route('events.show', $event) }}" 
                   class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                    View
                </a>
                
                @auth
                    @if(auth()->user()->role === 'organizer' && auth()->id() === $event->user_id)
                        <a href="{{ route('events.edit', $event) }}" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('events.destroy', $event) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"
                                    onclick="return confirm('Delete this event?')">
                                Delete
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-8">
            <p class="text-gray-500">No events found.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $events->links() }}
</div>
@endsection