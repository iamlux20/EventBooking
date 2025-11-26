@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Login</h2>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="mb-4">
            <input type="email" name="email" placeholder="Email" 
                   class="w-full p-3 border rounded @error('email') border-red-500 @enderror" 
                   value="{{ old('email') }}" required>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <input type="password" name="password" placeholder="Password" 
                   class="w-full p-3 border rounded @error('password') border-red-500 @enderror" required>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded hover:bg-blue-600">
            Login
        </button>
    </form>

    <p class="text-center mt-4">
        Don't have an account? <a href="{{ route('register') }}" class="text-blue-500">Register</a>
    </p>
</div>
@endsection