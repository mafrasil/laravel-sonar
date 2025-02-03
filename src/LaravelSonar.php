<?php

namespace Mafrasil\LaravelSonar;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mafrasil\LaravelSonar\Models\SonarEvent;

class LaravelSonar
{
    public function track(string $name, string $type, ?array $metadata = null)
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

    // Get event counts grouped by type
    public function getEventsByType( ? \DateTime $startDate = null,  ? \DateTime $endDate = null) : Collection
    {
        $query = SonarEvent::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->groupBy('type')
            ->select('type', DB::raw('count(*) as count'))
            ->get();
    }

    // Get most active pages
    public function getTopPages(int $limit = 10,  ? \DateTime $startDate = null) : Collection
    {
        $query = SonarEvent::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->groupBy('page')
            ->select('page', DB::raw('count(*) as count'))
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    // Get event timeline
    public function getEventTimeline(string $interval = '1 day',  ? \DateTime $startDate = null) : Collection
    {
        $query = SonarEvent::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        // Use different date formatting based on database driver
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            $dateFormat = "strftime('%Y-%m-%d', created_at)";
        } else {
            $dateFormat = "DATE_FORMAT(created_at, '%Y-%m-%d')";
        }

        return $query->groupBy(DB::raw($dateFormat))
            ->select([
                DB::raw("$dateFormat as date"),
                DB::raw('count(*) as count'),
            ])
            ->orderBy('date')
            ->get();
    }

    // Get most triggered events
    public function getTopEvents(int $limit = 10, ?string $type = null) : Collection
    {
        $query = SonarEvent::query();

        if ($type) {
            $query->where('type', $type);
        }

        return $query->groupBy('name')
            ->select('name', DB::raw('count(*) as count'))
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    // Get user engagement metrics
    public function getUserEngagement(): array
    {
        $totalEvents = SonarEvent::count();
        $uniquePages = SonarEvent::distinct('page')->count();
        $clickRate = SonarEvent::where('type', 'click')->count();
        $hoverRate = SonarEvent::where('type', 'hover')->count();

        return [
            'total_events' => $totalEvents,
            'unique_pages' => $uniquePages,
            'click_rate' => $clickRate,
            'hover_rate' => $hoverRate,
        ];
    }

    // Get element stats with conversion rates
    public function getElementStats(int $limit = 10): Collection
    {
        return DB::table('sonar_events')
            ->select(
                'name',
                DB::raw('SUM(CASE WHEN type = "impression" THEN 1 ELSE 0 END) as impressions'),
                DB::raw('SUM(CASE WHEN type = "hover" THEN 1 ELSE 0 END) as hovers'),
                DB::raw('SUM(CASE WHEN type = "click" THEN 1 ELSE 0 END) as clicks')
            )
            ->groupBy('name')
            ->orderByDesc('impressions')
            ->limit($limit)
            ->get()
            ->map(function ($stat) {
                return [
                    'name' => $stat->name,
                    'impressions' => $stat->impressions,
                    'hovers' => [
                        'count' => $stat->hovers,
                        'rate' => $stat->impressions > 0 ? ($stat->hovers / $stat->impressions) * 100 : 0,
                    ],
                    'clicks' => [
                        'count' => $stat->clicks,
                        'rate' => $stat->impressions > 0 ? ($stat->clicks / $stat->impressions) * 100 : 0,
                    ],
                ];
            });
    }

    // Get browser and device statistics
    public function getBrowserStats(): Collection
    {
        return DB::table('sonar_events')
            ->select('user_agent', DB::raw('count(*) as count'))
            ->groupBy('user_agent')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) {
                return [
                    'user_agent' => $stat->user_agent,
                    'count' => $stat->count,
                    'percentage' => $this->calculatePercentage($stat->count, DB::table('sonar_events')->count()),
                ];
            });
    }

    // Get screen size distribution
    public function getScreenSizeStats(): Collection
    {
        return DB::table('sonar_events')
            ->select('screen_size', DB::raw('count(*) as count'))
            ->groupBy('screen_size')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) {
                $size = json_decode($stat->screen_size);
                return [
                    'width' => $size->width,
                    'height' => $size->height,
                    'count' => $stat->count,
                    'percentage' => $this->calculatePercentage($stat->count, DB::table('sonar_events')->count()),
                ];
            });
    }

    // Get metadata analysis for an element
    public function getMetadataStats(string $name): Collection
    {
        return DB::table('sonar_events')
            ->where('name', $name)
            ->whereNotNull('metadata')
            ->get()
            ->groupBy(function ($event) {
                $metadata = is_string($event->metadata) ? json_decode($event->metadata) : $event->metadata;
                return json_encode($metadata);
            })
            ->map(function ($group) use ($name) {
                $metadata = $group->first()->metadata;
                $metadata = is_string($metadata) ? json_decode($metadata) : $metadata;
                return [
                    'metadata' => $metadata,
                    'count' => $group->count(),
                    'percentage' => $this->calculatePercentage($group->count(), DB::table('sonar_events')->where('name', $name)->count()),
                ];
            })
            ->values();
    }

    // Get page interaction statistics
    public function getPageStats(): Collection
    {
        return DB::table('sonar_events')
            ->select('page', DB::raw('count(*) as count'))
            ->groupBy('page')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) {
                return [
                    'page' => $stat->page,
                    'count' => $stat->count,
                    'percentage' => $this->calculatePercentage($stat->count, DB::table('sonar_events')->count()),
                ];
            });
    }

    private function calculatePercentage($count, $total): float
    {
        return $total > 0 ? round(($count / $total) * 100, 1) : 0;
    }
}
