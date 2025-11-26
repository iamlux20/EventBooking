@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Create Event</h2>
    
    <form method="POST" action="{{ route('events.store') }}">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Event Name</label>
            <input type="text" name="name" 
                   class="w-full p-3 border rounded @error('name') border-red-500 @enderror" 
                   value="{{ old('name') }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full p-3 border rounded @error('description') border-red-500 @enderror" 
                      required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Date & Time</label>
            <input type="datetime-local" name="date" 
                   class="w-full p-3 border rounded @error('date') border-red-500 @enderror" 
                   value="{{ old('date') }}" required>
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
            <input type="text" name="location" 
                   class="w-full p-3 border rounded @error('location') border-red-500 @enderror" 
                   value="{{ old('location') }}" required>
            @error('location')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-500 text-white p-3 rounded hover:bg-blue-600">
                Create Event
            </button>
            <a href="{{ route('events.index') }}" class="flex-1 bg-gray-500 text-white p-3 rounded text-center hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection