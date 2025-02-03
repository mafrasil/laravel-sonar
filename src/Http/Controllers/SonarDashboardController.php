<?php

namespace Mafrasil\LaravelSonar\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Mafrasil\LaravelSonar\Facades\LaravelSonar;
use Mafrasil\LaravelSonar\Models\SonarEvent;

class SonarDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(\Mafrasil\LaravelSonar\Http\Middleware\AuthorizeSonar::class);
    }

    public function index()
    {
        return view('sonar::dashboard', [
            'elementStats' => LaravelSonar::getElementStats(20),
            'eventsByType' => LaravelSonar::getEventsByType(now()->subDays(30)),
            'topPages' => LaravelSonar::getTopPages(5),
            'dailyEvents' => LaravelSonar::getEventTimeline(),
            'engagement' => LaravelSonar::getUserEngagement(),
        ]);
    }

    public function show(string $elementName)
    {
        $events = SonarEvent::where('name', $elementName)->get();

        $data = [
            'name' => $elementName,
            'summary' => [
                'total_events' => $events->count(),
                'impressions' => $events->where('type', 'impression')->count(),
                'hovers' => $events->where('type', 'hover')->count(),
                'clicks' => $events->where('type', 'click')->count(),
            ],
            'timeline' => $this->getElementTimeline($elementName),
            'recentEvents' => SonarEvent::where('name', $elementName)->latest()->paginate(20),
            'metadataStats' => LaravelSonar::getMetadataStats($elementName),
            'browserStats' => LaravelSonar::getBrowserStats(),
            'screenSizeStats' => LaravelSonar::getScreenSizeStats(),
        ];

        return view('sonar::element-details', $data);
    }

    protected function getElementTimeline(string $elementName)
    {
        $driver = config('database.default');
        $dateFormat = $driver === 'sqlite' ? "strftime('%Y-%m-%d', created_at)" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

        return SonarEvent::where('name', $elementName)
            ->groupBy(DB::raw($dateFormat))
            ->select([
                DB::raw("$dateFormat as date"),
                DB::raw('count(*) as count'),
            ])
            ->orderBy('date')
            ->get();
    }
}
