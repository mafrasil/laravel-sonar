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
                'location' => $event['location'],
                'platform' => $event['platform'],
                'client_timestamp' => $event['timestamp'],
            ]);
        });

        return response()->json(['status' => 'success']);
    }
}
