<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonar Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-[1500px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Sonar Analytics</h1>
                <p class="mt-1 text-sm text-gray-500">Real-time analytics and insights for your application.</p>
            </div>
        </div>

        <!-- Element Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Table Section -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-5">
                    <h2 class="text-lg font-medium text-gray-900 mb-1">Element Statistics</h2>
                    <p class="text-sm text-gray-500 mb-4">Track performance metrics for each element</p>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Name</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Impressions</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Hovers</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Clicks</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elementStats as $stat)
                                <tr class="hover:bg-gray-50 border-b border-gray-100">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $stat['name'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($stat['impressions']) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium">{{ number_format($stat['hovers']['count']) }}</span>
                                        <span class="text-gray-500 text-xs">({{ number_format($stat['hovers']['rate'], 1) }}%)</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium">{{ number_format($stat['clicks']['count']) }}</span>
                                        <span class="text-gray-500 text-xs">({{ number_format($stat['clicks']['rate'], 1) }}%)</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('sonar.element.show', ['elementName' => $stat['name']]) }}" 
                                           class="text-black hover:text-gray-600 font-medium">Details</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Events by Type -->
            <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-1">Events by Type</h2>
                <p class="text-sm text-gray-500 mb-4">Distribution of event categories</p>
                <div style="height: 300px">
                    <canvas id="eventsByTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Timeline Chart -->
        <div class="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium text-gray-900 mb-1">Daily Events Timeline</h2>
            <p class="text-sm text-gray-500 mb-4">Event frequency over time</p>
            <div style="height: 300px">
                <canvas id="dailyEventsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.borderColor = '#e5e7eb';
        Chart.defaults.color = '#737373';

        new Chart(document.getElementById('dailyEventsChart'), {
            type: 'line',
            data: {
                labels: @json($dailyEvents->pluck('date')),
                datasets: [{
                    label: 'Events',
                    data: @json($dailyEvents->pluck('count')),
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

        new Chart(document.getElementById('eventsByTypeChart'), {
            type: 'doughnut',
            data: {
                labels: @json($eventsByType->pluck('type')),
                datasets: [{
                    data: @json($eventsByType->pluck('count')),
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
    </script>
</body>
</html>