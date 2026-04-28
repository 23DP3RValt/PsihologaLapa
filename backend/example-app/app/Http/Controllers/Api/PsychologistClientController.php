<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Psychologist;
use App\Models\PsychologistClientComment;
use App\Models\User;
use Illuminate\Http\Request;

class PsychologistClientController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        if (! $actor instanceof Psychologist) {
            return response()->json(['error' => 'Only psychologists can access this data.'], 403);
        }

        $signedEvents = Event::with(['client'])
            ->where('psychologist_id', $actor->id)
            ->whereNotNull('client_id')
            ->orderByDesc('start')
            ->get();

        $comments = PsychologistClientComment::where('psychologist_id', $actor->id)
            ->orderByDesc('created_at')
            ->get();

        $clients = User::orderBy('name')->get()->map(function (User $client) use ($signedEvents, $comments) {
            $clientEvents = $signedEvents->where('client_id', $client->id);
            $clientComments = $comments->where('client_id', $client->id)->map(function (PsychologistClientComment $comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'event_id' => $comment->event_id,
                    'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : null,
                ];
            })->values();

            $lastComment = $clientComments->first();

            return [
                'id' => $client->id,
                'name' => trim($client->name . ' ' . $client->surname),
                'email' => $client->email,
                'talrunis' => $client->talrunis,
                'birthdate' => $client->birthdate,
                'participation_count' => $clientEvents->count(),
                'participation_summary' => [
                    'konsultacija' => $clientEvents->where('event_type', 'konsultacija')->count(),
                    'petijums' => $clientEvents->where('event_type', 'petijums')->count(),
                ],
                'signed_up_events' => $clientEvents->map(function (Event $event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'event_type' => $event->event_type,
                        'start' => $event->start ? $event->start->toDateTimeString() : null,
                        'end' => $event->end ? $event->end->toDateTimeString() : null,
                        'client_note' => $event->client_note,
                    ];
                })->values(),
                'psychologist_comments' => $clientComments,
                'last_comment' => $lastComment ? $lastComment['comment'] : null,
            ];
        });

        return response()->json(['clients' => $clients]);
    }

    public function storeComment(Request $request, $clientId)
    {
        $actor = $request->user();
        if (! $actor instanceof Psychologist) {
            return response()->json(['error' => 'Only psychologists can add comments.'], 403);
        }

        $client = User::findOrFail($clientId);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $comment = PsychologistClientComment::create([
            'psychologist_id' => $actor->id,
            'client_id' => $client->id,
            'event_id' => $validated['event_id'] ?? null,
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'id' => $comment->id,
            'client_id' => $comment->client_id,
            'comment' => $comment->comment,
            'event_id' => $comment->event_id,
            'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : null,
        ], 201);
    }

    public function clientProfile(Request $request)
    {
        $actor = $request->user();
        if (! $actor instanceof User) {
            return response()->json(['error' => 'Only clients can access this data.'], 403);
        }

        $signedEvents = Event::with(['psychologist'])
            ->where('client_id', $actor->id)
            ->orderByDesc('start')
            ->get();

        $comments = PsychologistClientComment::where('client_id', $actor->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (PsychologistClientComment $comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'event_id' => $comment->event_id,
                    'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : null,
                ];
            });

        return response()->json([
            'profile' => [
                'id' => $actor->id,
                'name' => trim($actor->name . ' ' . $actor->surname),
                'email' => $actor->email,
                'talrunis' => $actor->talrunis,
                'birthdate' => $actor->birthdate,
                'signed_up_events' => $signedEvents->map(function (Event $event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'event_type' => $event->event_type,
                        'start' => $event->start ? $event->start->toDateTimeString() : null,
                        'end' => $event->end ? $event->end->toDateTimeString() : null,
                        'psychologist_name' => $event->psychologist?->name,
                        'client_note' => $event->client_note,
                    ];
                })->values(),
                'psychologist_comments' => $comments,
            ],
        ]);
    }
}
