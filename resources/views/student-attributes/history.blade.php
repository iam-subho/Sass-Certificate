@extends('layouts.app')

@section('title', 'Attribute History')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .chart-container-large {
        position: relative;
        height: 400px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8" x-data="chartTabs()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Attribute History</h1>
            <p class="mt-1 text-sm text-gray-500">Student: <strong>{{ $student->full_name }}</strong></p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('student-attributes.create', $student) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Assign New
            </a>
            <a href="{{ route('student-attributes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
    @endif

    <!-- Student Info Card -->
    <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <img class="h-12 w-12 rounded-full" src="{{ $student->profile_picture_url }}" alt="{{ $student->full_name }}">
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">{{ $student->full_name }}</h3>
                <p class="text-sm text-gray-500">{{ $student->email }} | Class: {{ $student->class->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    @if(count($chartData['attributes']) > 0)
    <!-- Charts Section -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                <button @click="activeTab = 'combined'"
                    :class="activeTab === 'combined' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    All Attributes
                </button>
                @foreach($chartData['attributes'] as $attr)
                <button @click="activeTab = 'attr_{{ $attr['id'] }}'"
                    :class="activeTab === 'attr_{{ $attr['id'] }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors flex items-center">
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
                    <strong>Note:</strong> For optimal performance, charts display only the latest <strong>{{ $chartData['maxPoints'] }}</strong> data points per attribute.
                    Use the filters below to view specific date ranges.
                </p>
            </div>
            @endif

            <!-- Combined Chart -->
            <div x-show="activeTab === 'combined'" x-cloak>
                <h3 class="text-lg font-medium text-gray-900 mb-4">All Attributes Overview</h3>
                <p class="text-sm text-gray-500 mb-4">Showing up to {{ $chartData['maxPoints'] }} data points per attribute</p>
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
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $attr['name'] }}</h3>
                        <p class="text-sm text-gray-500">
                            Type: <span class="px-2 py-0.5 text-xs rounded-full {{ $attr['type'] === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($attr['type']) }}
                            </span>
                            | Range: {{ $attr['min'] }} - {{ $attr['max'] }}
                            @if($attr['truncated'])
                            | <span class="text-amber-600">Showing {{ count($attr['data']) }} of {{ $attr['totalCount'] }} points</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold" style="color: {{ $attr['color'] }}">{{ end($attr['data']) }}</p>
                        <p class="text-xs text-gray-500">Latest Score</p>
                    </div>
                </div>
                <div class="chart-container-large">
                    <canvas id="chart_{{ $attr['id'] }}"></canvas>
                </div>
                <!-- Stats -->
                <div class="grid grid-cols-4 gap-4 mt-4">
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
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-yellow-700">No attribute data available for charts. <a href="{{ route('student-attributes.create', $student) }}" class="underline font-medium">Assign attributes</a> to see visualizations.</p>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
        <form method="GET" action="{{ route('student-attributes.history', $student) }}" class="flex flex-wrap items-end gap-4">
            <div class="w-48">
                <label for="attribute_id" class="block text-sm font-medium text-gray-700">Attribute</label>
                <select name="attribute_id" id="attribute_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Attributes</option>
                    @foreach($attributes as $attr)
                    <option value="{{ $attr->id }}" {{ request('attribute_id') == $attr->id ? 'selected' : '' }}>{{ $attr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('student-attributes.history', $student) }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Detailed History</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attribute</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assignments as $assignment)
                <tr>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $assignment->assignedByUser->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ Str::limit($assignment->remarks, 30) ?: '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('student-attributes.edit', $assignment) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <form action="{{ route('student-attributes.destroy', $assignment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No attribute assignments found.
                        <a href="{{ route('student-attributes.create', $student) }}" class="text-blue-600 hover:underline">Assign one now</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $assignments->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
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

            // Find the longest dataset for x-axis labels
            let maxLength = 0;
            let longestLabels = [];
            this.chartData.attributes.forEach(attr => {
                if (attr.labels.length > maxLength) {
                    maxLength = attr.labels.length;
                    longestLabels = attr.labels;
                }
            });

            // Create datasets
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
@endpush
