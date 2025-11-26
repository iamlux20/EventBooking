<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required'],
            'price' => ['required'],
            'quantity' => ['required'],
            'event_id' => ['required']
        ]);
        $ticket = Ticket::create([
            'type' => $request->type,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'event_id' => $request->event_id

        ]);

        return response()->json($ticket);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'type' => ['required'],
            'price' => ['required'],
            'quantity' => ['required'],
            'event_id' => ['required']
        ]);

        $ticket->update([
            'type' => $request->type,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'event_id' => $request->event_id
        ]);

        return response()->json($ticket);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
