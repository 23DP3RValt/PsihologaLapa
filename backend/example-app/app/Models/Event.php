<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'start',
        'end',
        'description',
        'color'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function toArray()
    {
        $array = parent::toArray();
        
        // Format dates as ISO-8601 strings for FullCalendar
        if ($this->start) {
            $array['start'] = $this->start->toIso8601String();
        }
        if ($this->end) {
            $array['end'] = $this->end->toIso8601String();
        }
        
        return $array;
    }
}
