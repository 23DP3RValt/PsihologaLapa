<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Psychologist extends Model
{
    protected $fillable = [
        'name',
        'email',
        'specialization',
        'bio'
    ];
}
