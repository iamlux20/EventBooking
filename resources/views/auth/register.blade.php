@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Register</h2>
    
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="mb-4">
            <input type="text" name="name" placeholder="Name" 
                   class="w-full p-3 border rounded @error('name') border-red-500 @enderror" 
                   value="{{ old('name') }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <input type="email" name="email" placeholder="Email" 
                   class="w-full p-3 border rounded @error('email') border-red-500 @enderror" 
                   value="{{ old('email') }}" required>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <input type="password" name="password" placeholder="Password" 
                   class="w-full p-3 border rounded @error('password') border-red-500 @enderror" required>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" 
                   class="w-full p-3 border rounded" required>
        </div>

        <div class="mb-6">
            <select name="role" class="w-full p-3 border rounded @error('role') border-red-500 @enderror" required>
                <option value="">Select Role</option>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="organizer" {{ old('role') == 'organizer' ? 'selected' : '' }}>Organizer</option>
            </select>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-green-500 text-white p-3 rounded hover:bg-green-600">
            Register
        </button>
    </form>

    <p class="text-center mt-4">
        Already have an account? <a href="{{ route('login') }}" class="text-blue-500">Login</a>
    </p>
</div>
@endsection