<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function show(Payment $payment)
    {
        return response()->json($payment, 200);
    }

    public function store(Request $request, $id, PaymentService $paymentService)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $status = $paymentService->processPayment();
        $payment = Payment::create([
            'booking_id' => $id,
            'amount' => $request->amount,
            'status' => $status ? PaymentStatus::SUCCESS->value : PaymentStatus::FAILED->value
        ]);

        if ($status) {
            Booking::where('id', $id)->update(['status' => BookingStatus::CONFIRMED->value]);
        }

        return $status ? response()->json($payment, 201) : response()->json(['message' => 'Payment failed!'], 418);
    }
}
