<?php

namespace Mafrasil\LaravelSonar\Models;

use Illuminate\Database\Eloquent\Model;

class SonarEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'screen_size' => 'array',
        'client_timestamp' => 'datetime',
    ];
}
