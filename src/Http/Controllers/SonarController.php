<?php

namespace Mafrasil\LaravelSonar\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mafrasil\LaravelSonar\Models\SonarEvent;

class SonarController extends Controller
{
    public function store(Request $request)
    {
        collect($request->events)->each(function ($event) {
            SonarEvent::create([
                'name' => $event['name'],
                'type' => $event['type'],
                'metadata' => $event['metadata'] ?? null,
                'page' => $event['page'],
                'user_agent' => $event['userAgent'],
                'screen_size' => $event['screenSize'],
                'client_timestamp' => $event['timestamp'],
            ]);
        });

        return response()->json(['status' => 'success']);
    }
}
