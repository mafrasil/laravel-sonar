<?php

namespace Mafrasil\LaravelSonar;

use Mafrasil\LaravelSonar\Models\SonarEvent;

class LaravelSonar
{
    public function track(string $name, string $type, array $metadata = null)
    {
        return SonarEvent::create([
            'name' => $name,
            'type' => $type,
            'metadata' => $metadata,
            'page' => request()->path(),
            'user_agent' => request()->userAgent(),
            'screen_size' => ['width' => 0, 'height' => 0],
            'client_timestamp' => now(),
        ]);
    }
}
