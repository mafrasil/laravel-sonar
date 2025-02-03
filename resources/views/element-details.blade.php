<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonar - Element Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-[1500px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Element: {{ $name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Detailed analytics and insights</p>
            </div>
            <a href="{{ route('sonar.dashboard') }}" 
               class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors duration-200 inline-flex items-center text-sm">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @foreach($summary as $key => $value)
            <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ Str::title($key) }}</p>
                <p class="text-2xl font-semibold text-gray-900 mt-2">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        <!-- Timeline Chart -->
        <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-1">Event Timeline</h2>
            <p class="text-sm text-gray-500 mb-4">Event frequency over time</p>
            <div style="height: 300px">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>

        <!-- Device & Browser Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-1">Browser Distribution</h2>
                <p class="text-sm text-gray-500 mb-4">User browser statistics</p>
                <div style="height: 300px">
                    <canvas id="browserChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-1">Screen Sizes</h2>
                <p class="text-sm text-gray-500 mb-4">Viewport dimensions distribution</p>
                <div style="height: 300px">
                    <canvas id="screenSizeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Events Table -->
        <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-1">Recent Events</h2>
            <p class="text-sm text-gray-500 mb-4">Latest interactions with this element</p>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Metadata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvents as $event)
                        <tr class="hover:bg-gray-50 border-b border-gray-100">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $event->type }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $event->location }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $event->created_at }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
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
            <div class="mt-4">
                {{ $recentEvents->links() }}
            </div>
        </div>

        <!-- Metadata Analysis -->
        @if($metadataStats->isNotEmpty())
        <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium text-gray-900 mb-1">Metadata Analysis</h2>
            <p class="text-sm text-gray-500 mb-4">Statistical breakdown of metadata patterns</p>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Metadata</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Count</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metadataStats as $stat)
                        <tr class="hover:bg-gray-50 border-b border-gray-100">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <pre class="whitespace-pre-wrap">{{ json_encode($stat['metadata'], JSON_PRETTY_PRINT) }}</pre>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-right">
                                {{ number_format($stat['count']) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-right">
                                {{ number_format($stat['percentage'], 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <script>
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.borderColor = '#e5e7eb';
        Chart.defaults.color = '#737373';

        new Chart(document.getElementById('timelineChart'), {
            type: 'line',
            data: {
                labels: @json($timeline->pluck('date')),
                datasets: [{
                    label: 'Events',
                    data: @json($timeline->pluck('count')),
                    borderColor: '#000000',
                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('browserChart'), {
            type: 'doughnut',
            data: {
                labels: @json($browserStats->pluck('user_agent')),
                datasets: [{
                    data: @json($browserStats->pluck('count')),
                    backgroundColor: [
                        '#000000',
                        '#404040',
                        '#737373',
                        '#a3a3a3'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                cutout: '75%'
            }
        });

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
                    backgroundColor: 'rgba(0, 0, 0, 0.5)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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