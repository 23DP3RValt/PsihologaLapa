<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'title',
        'start',
        'end',
        'description',
        'color',
        'event_type',
        'psychologist_id',
        'client_id',
        'client_note'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'psychologist_id' => 'integer',
        'client_id' => 'integer'
    ];

    protected $appends = [
        'is_booked',
        'psychologist_name',
        'client_name'
    ];

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function getIsBookedAttribute(): bool
    {
        return !empty($this->client_id);
    }

    public function getPsychologistNameAttribute(): ?string
    {
        return $this->psychologist ? $this->psychologist->name : null;
    }

    public function getClientNameAttribute(): ?string
    {
        if (! $this->client) {
            return null;
        }

        return trim($this->client->name . ' ' . $this->client->surname);
    }

    public function toArray()
    {
        $array = parent::toArray();

        if ($this->start) {
            $array['start'] = $this->start->format('Y-m-d\TH:i:s');
        }
        if ($this->end) {
            $array['end'] = $this->end->format('Y-m-d\TH:i:s');
        }

        return $array;
    }
}
