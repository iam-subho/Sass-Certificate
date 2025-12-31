@extends('student.layouts.app')

@section('title', 'My Progress')

@section('content')
<div class="space-y-6" x-data="chartTabs()">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Progress</h1>
                <p class="text-gray-600 mt-1">Track your attribute scores and performance over time</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    @if(count($summary) > 0)
    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($summary as $stat)
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 truncate">{{ $stat['attribute_name'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stat['latest_value'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $stat['total_assignments'] }} record(s) | Avg: {{ $stat['avg_score'] ?? 'N/A' }}
                    </p>
                </div>
                <div class="ml-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full {{ $stat['attribute_type'] === 'enum' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }}">
                        @if($stat['attribute_type'] === 'enum')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if(count($chartData['attributes']) > 0)
    <!-- Charts Section -->
    <div class="bg-white rounded-lg shadow-md">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto px-4" aria-label="Tabs">
                <button @click="activeTab = 'combined'"
                    :class="activeTab === 'combined' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition-colors">
                    Overview
                </button>
                @foreach($chartData['attributes'] as $attr)
                <button @click="activeTab = 'attr_{{ $attr['id'] }}'"
                    :class="activeTab === 'attr_{{ $attr['id'] }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center">
                    <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $attr['color'] }}"></span>
                    {{ $attr['name'] }}
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Data Truncation Notice -->
            @if($chartData['hasMoreData'])
            <div class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-3 flex items-start">
                <svg class="w-5 h-5 text-amber-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-amber-700">
                    <strong>Note:</strong> Charts display the latest <strong>{{ $chartData['maxPoints'] }}</strong> data points per attribute.
                    Use the filters below to view specific date ranges.
                </p>
            </div>
            @endif

            <!-- Combined Chart -->
            <div x-show="activeTab === 'combined'" x-cloak>
                <h3 class="text-lg font-medium text-gray-900 mb-2">All Attributes Overview</h3>
                <p class="text-sm text-gray-500 mb-4">Your progress across all tracked attributes</p>
                <div class="chart-container-large">
                    <canvas id="combinedChart"></canvas>
                </div>
                <!-- Legend -->
                <div class="flex flex-wrap gap-4 mt-4 justify-center">
                    @foreach($chartData['attributes'] as $attr)
                    <div class="flex items-center">
                        <span class="w-4 h-4 rounded mr-2" style="background-color: {{ $attr['color'] }}"></span>
                        <span class="text-sm text-gray-600">{{ $attr['name'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Individual Attribute Charts -->
            @foreach($chartData['attributes'] as $attr)
            <div x-show="activeTab === 'attr_{{ $attr['id'] }}'" x-cloak>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $attr['name'] }}</h3>
                        <p class="text-sm text-gray-500">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full {{ $attr['type'] === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($attr['type']) }}
                            </span>
                            <span class="ml-2">Range: {{ $attr['min'] }} - {{ $attr['max'] }}</span>
                            @if($attr['truncated'])
                            <span class="ml-2 text-amber-600">| Showing {{ count($attr['data']) }} of {{ $attr['totalCount'] }} points</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-3xl font-bold" style="color: {{ $attr['color'] }}">{{ end($attr['data']) }}</p>
                        <p class="text-xs text-gray-500">Latest Score</p>
                    </div>
                </div>
                <div class="chart-container-large">
                    <canvas id="chart_{{ $attr['id'] }}"></canvas>
                </div>
                <!-- Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Min</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format(min($attr['data']), 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Max</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format(max($attr['data']), 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Average</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format(array_sum($attr['data']) / count($attr['data']), 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Data Points</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($attr['data']) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No attribute data yet</h3>
        <p class="mt-1 text-sm text-gray-500">Your progress will appear here once your school assigns attributes to you.</p>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Filter History</h3>
        <form method="GET" action="{{ route('student.history') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-48">
                <label for="attribute_id" class="block text-sm font-medium text-gray-700">Attribute</label>
                <select name="attribute_id" id="attribute_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Attributes</option>
                    @foreach($attributes as $attr)
                    <option value="{{ $attr->id }}" {{ request('attribute_id') == $attr->id ? 'selected' : '' }}>{{ $attr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="w-full sm:w-auto">
                <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    Apply Filter
                </button>
                <a href="{{ route('student.history') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Detailed History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attribute</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $assignment->assigned_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $assignment->attribute->name }}</div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->attribute->type->value === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $assignment->attribute->type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $assignment->display_value }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($assignment->numeric_score, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ $assignment->remarks ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            No attribute records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $assignments->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .chart-container-large {
        position: relative;
        height: 350px;
        width: 100%;
    }
    @media (max-width: 640px) {
        .chart-container-large {
            height: 250px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function chartTabs() {
    return {
        activeTab: 'combined',
        charts: {},
        chartData: @json($chartData),

        init() {
            this.$nextTick(() => {
                if (this.chartData.attributes.length > 0) {
                    this.initCombinedChart();
                    this.initIndividualCharts();
                }
            });
        },

        initCombinedChart() {
            const ctx = document.getElementById('combinedChart');
            if (!ctx) return;

            let maxLength = 0;
            let longestLabels = [];
            this.chartData.attributes.forEach(attr => {
                if (attr.labels.length > maxLength) {
                    maxLength = attr.labels.length;
                    longestLabels = attr.labels;
                }
            });

            const datasets = this.chartData.attributes.map(attr => ({
                label: attr.name,
                data: attr.data,
                borderColor: attr.color,
                backgroundColor: attr.color + '20',
                borderWidth: 2,
                fill: false,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
            }));

            this.charts.combined = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: longestLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const attr = this.chartData.attributes[context.datasetIndex];
                                    const tooltip = attr.tooltips[context.dataIndex] || context.parsed.y;
                                    return `${context.dataset.label}: ${tooltip} (${context.parsed.y})`;
                                }.bind(this)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Score'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    }
                }
            });
        },

        initIndividualCharts() {
            this.chartData.attributes.forEach(attr => {
                const ctx = document.getElementById('chart_' + attr.id);
                if (!ctx) return;

                this.charts['attr_' + attr.id] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: attr.labels,
                        datasets: [{
                            label: attr.name,
                            data: attr.data,
                            borderColor: attr.color,
                            backgroundColor: attr.color + '20',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointBackgroundColor: attr.color,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const tooltip = attr.tooltips[context.dataIndex] || context.parsed.y;
                                        return `Value: ${tooltip} (Score: ${context.parsed.y})`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                min: attr.min * 0.9,
                                max: attr.max * 1.1,
                                title: {
                                    display: true,
                                    text: 'Score'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        }
                    }
                });
            });
        }
    }
}
</script>
@endsection
