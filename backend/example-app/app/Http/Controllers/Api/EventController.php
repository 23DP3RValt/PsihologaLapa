<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::with(['psychologist', 'client'])->get();
            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $actor = $request->user();
            if (! $actor instanceof Psychologist) {
                return response()->json(['error' => 'Only psychologists can create events.'], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'event_type' => 'required|string|in:konsultacija,petijums',
                'start' => 'required|string',
                'end' => 'nullable|string',
                'description' => 'nullable|string',
                'color' => 'nullable|string'
            ]);

            try {
                $validated['start'] = $this->parseDate($validated['start']);
                $validated['end'] = $validated['end'] ? $this->parseDate($validated['end']) : $validated['start'];
            } catch (\Exception $e) {
                \Log::error('Date parsing error: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 422);
            }

            $validated['psychologist_id'] = $actor->id;
            $event = Event::create($validated);

            return response()->json($event, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Event store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function signup(Request $request, $id)
    {
        try {
            $actor = $request->user();
        if (! $actor instanceof User) {
            }

            $validated = $request->validate([
                'client_note' => 'nullable|string',
            ]);

            $event = Event::findOrFail($id);
            if ($event->client_id) {
                return response()->json(['error' => 'This event is already reserved.'], 409);
            }

            $event->client_id = $actor->id;
            $event->client_note = $validated['client_note'] ?? null;
            $event->save();

            return response()->json($event);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Event signup error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $actor = $request->user();
            if (! $actor instanceof Psychologist) {
                return response()->json(['error' => 'Only psychologists can update events.'], 403);
            }

            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'event_type' => 'nullable|string|in:konsultacija,petijums',
                'start' => 'nullable|string',
                'end' => 'nullable|string',
                'description' => 'nullable|string',
                'color' => 'nullable|string'
            ]);

            if (isset($validated['start']) && $validated['start']) {
                $validated['start'] = $this->parseDate($validated['start']);
            }
            if (isset($validated['end']) && $validated['end']) {
                $validated['end'] = $this->parseDate($validated['end']);
            }

            $event = Event::findOrFail($id);
            if ($event->psychologist_id !== $actor->id) {
                return response()->json(['error' => 'You can only update your own events.'], 403);
            }

            $event->update(array_filter($validated));
            return response()->json($event);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request, $id)
    {
        $actor = $request->user();
        if (! $actor instanceof Psychologist) {
            return response()->json(['error' => 'Only psychologists can delete events.'], 403);
        }

        $event = Event::findOrFail($id);
        if ($event->psychologist_id !== $actor->id) {
            return response()->json(['error' => 'You can only delete your own events.'], 403);
        }

        Event::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    private function parseDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    private function getActor(Request $request): array
    {
        return [
            'role' => strtolower($request->header('X-User-Role', '')),
            'id' => intval($request->header('X-User-Id', 0)),
            'name' => $request->header('X-User-Name', ''),
        ];
    }
}
