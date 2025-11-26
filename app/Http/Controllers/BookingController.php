<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())->paginate(20);
        return response()->json($bookings);
    }

    public function store(Request $request, $ticket_id)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'ticket_id' => $ticket_id,
            'quantity' => $request->quantity,
            'status' => BookingStatus::PENDING->value,
        ]);
        return response()->json($booking, 201);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $booking->update(['status' => BookingStatus::CANCELLED->value]);
        return response()->json($booking, 200);
    }
}
