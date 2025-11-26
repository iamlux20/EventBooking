<?php

namespace App\Http\Middleware;

use App\Enums\BookingStatus;
use Closure;
use App\Models\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    public function handle(Request $request, Closure $next): Response
    {
        $ticketId = $request->route('id');
        $userId = $request->user()->id;

        $existingBooking = Booking::where('user_id', $userId)
            ->where('ticket_id', $ticketId)
            ->where('status', '!=', BookingStatus::CANCELLED->value)
            ->exists();

        if ($existingBooking) {
            return response()->json([
                'message' => 'You already have an active booking for this ticket'
            ], 409);
        }

        return $next($request);
    }
}
