<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychologistClientComment extends Model
{
    protected $fillable = [
        'psychologist_id',
        'client_id',
        'event_id',
        'comment',
    ];

    protected $casts = [
        'psychologist_id' => 'integer',
        'client_id' => 'integer',
        'event_id' => 'integer',
    ];

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
