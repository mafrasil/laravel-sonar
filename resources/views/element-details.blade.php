<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonar - Element Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Element: {{ $name }}</h1>
                <a href="{{ route('sonar.dashboard') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Dashboard</a>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @foreach($summary as $key => $value)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">{{ Str::title($key) }}</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $value }}</dd>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Timeline Chart -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Event Timeline</h2>
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>

            <!-- Recent Events with Metadata -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Events</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metadata</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentEvents as $event)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $event->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $event->page }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $event->created_at }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($event->metadata)
                                            <pre class="whitespace-pre-wrap">{{ json_encode($event->metadata, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $recentEvents->links() }}
                </div>
            </div>

            <!-- Metadata Analysis -->
            @if($metadataStats->isNotEmpty())
            <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Metadata Analysis</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metadata</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($metadataStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <pre class="whitespace-pre-wrap">{{ json_encode($stat['metadata'], JSON_PRETTY_PRINT) }}</pre>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ number_format($stat['count']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ number_format($stat['percentage'], 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Device & Browser Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Browser Distribution</h2>
                        <canvas id="browserChart"></canvas>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Screen Sizes</h2>
                        <canvas id="screenSizeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('timelineChart'), {
            type: 'line',
            data: {
                labels: @json($timeline->pluck('date')),
                datasets: [{
                    label: 'Events',
                    data: @json($timeline->pluck('count')),
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1
                }]
            }
        });

        // Browser Chart
        new Chart(document.getElementById('browserChart'), {
            type: 'pie',
            data: {
                labels: @json($browserStats->pluck('user_agent')),
                datasets: [{
                    data: @json($browserStats->pluck('count')),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            }
        });

        // Screen Size Chart
        new Chart(document.getElementById('screenSizeChart'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Screen Sizes',
                    data: @json($screenSizeStats->map(function($stat) {
                        return [
                            'x' => $stat['width'],
                            'y' => $stat['height'],
                            'r' => $stat['count']
                        ];
                    })),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)'
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Width (px)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Height (px)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 