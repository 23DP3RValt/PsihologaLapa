<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::all();
            return response()->json($events->makeVisible(['id', 'title', 'start', 'end', 'description', 'color']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start' => 'required|string',
                'end' => 'nullable|string',
                'description' => 'nullable|string',
                'color' => 'nullable|string'
            ]);
            
            // Convert string dates to datetime
            try {
                $validated['start'] = $this->parseDate($validated['start']);
                $validated['end'] = $validated['end'] ? $this->parseDate($validated['end']) : $validated['start'];
            } catch (\Exception $e) {
                \Log::error('Date parsing error: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 422);
            }
            
            $event = Event::create($validated);
            return response()->json($event, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Event store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function parseDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'start' => 'nullable|string',
                'end' => 'nullable|string',
                'description' => 'nullable|string',
                'color' => 'nullable|string'
            ]);
            
            // Parse dates if provided
            if (isset($validated['start']) && $validated['start']) {
                $validated['start'] = $this->parseDate($validated['start']);
            }
            if (isset($validated['end']) && $validated['end']) {
                $validated['end'] = $this->parseDate($validated['end']);
            }
            
            $event = Event::findOrFail($id);
            $event->update(array_filter($validated));
            return $event;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        Event::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
