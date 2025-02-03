<?php

namespace Mafrasil\LaravelSonar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SonarStatsCommand extends Command
{
    protected $signature = 'sonar:stats {--limit=10}';
    protected $description = 'Display Sonar analytics in a table format';

    public function handle()
    {
        $stats = $this->getStats();

        $headers = ['#', 'Name', 'Impressions', 'Hovers', 'Clicks'];
        $rows = [];

        foreach ($stats as $index => $stat) {
            $rows[] = [
                $index + 1,
                $stat->name,
                number_format($stat->impressions),
                sprintf(
                    "%s (%s%%)",
                    number_format($stat->hovers),
                    number_format(($stat->impressions > 0 ? ($stat->hovers / $stat->impressions) * 100 : 0), 1)
                ),
                sprintf(
                    "%s (%s%%)",
                    number_format($stat->clicks),
                    number_format(($stat->impressions > 0 ? ($stat->clicks / $stat->impressions) * 100 : 0), 1)
                ),
            ];
        }

        $this->table($headers, $rows);
    }

    protected function getStats()
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
            ->limit($this->option('limit'))
            ->get();
    }
}
