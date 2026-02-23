<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        return Event::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after:start',
            'description' => 'nullable|string',
            'color' => 'nullable|string'
        ]);
        return Event::create($validated);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after:start',
            'description' => 'nullable|string',
            'color' => 'nullable|string'
        ]);
        $event = Event::findOrFail($id);
        $event->update($validated);
        return $event;
    }

    public function destroy($id)
    {
        Event::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
