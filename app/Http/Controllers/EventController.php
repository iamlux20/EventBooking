<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index()
    {
        $currentPage = request()->get('page', 1);

        $events = Cache::remember('events-' . $currentPage, 10, function () {
            return DB::table('events')->paginate(25);
        });

        return response()->json($events, 200);
    }

    public function show(Event $event)
    {
        return response()->json($event, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string']
        ]);

        $event = Event::create($validated);
        return response()->json($event, 201);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string']
        ]);

        $event->update($validated);
        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
