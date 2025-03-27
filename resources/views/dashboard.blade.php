<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Range Picker -->
            <div class="flex justify-end mb-4">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-2">
                    <div class="flex items-center space-x-2 bg-white rounded-md border px-3 py-2">
                        <span class="text-sm text-gray-700">Custom Range</span>
                        <button type="button" onclick="this.form.reset();this.form.submit();" class="text-gray-400 hover:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <input type="date" name="start_date" class="border rounded-md p-2 pr-10 text-sm" value="{{ $startDate->format('Y-m-d') }}">
                            <div class="absolute right-2 top-1/2 -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            </div>
                        </div>
                        
                        <span class="text-gray-700">to</span>
                        
                        <div class="relative">
                            <input type="date" name="end_date" class="border rounded-md p-2 pr-10 text-sm" value="{{ $endDate->format('Y-m-d') }}">
                            <div class="absolute right-2 top-1/2 -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <x-ui-card class="bg-white">
                    <div class="flex flex-col space-y-1">
                        <div class="text-sm font-medium text-gray-500">Total Sales</div>
                        <div class="text-2xl font-bold">${{ number_format($stats['total_active_vehicles'] * 100, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">— 0% vs previous period</div>
                    </div>
                </x-ui-card>
                
                <x-ui-card class="bg-white">
                    <div class="flex flex-col space-y-1">
                        <div class="text-sm font-medium text-gray-500">Total Orders</div>
                        <div class="text-2xl font-bold">{{ $stats['frontline_ready_vehicles'] }}</div>
                        <div class="text-xs text-gray-500 mt-1">— 0% vs previous period</div>
                    </div>
                </x-ui-card>
                
                <x-ui-card class="bg-white">
                    <div class="flex flex-col space-y-1">
                        <div class="text-sm font-medium text-gray-500">Average Order Value</div>
                        <div class="text-2xl font-bold">${{ number_format($stats['frontline_ready_vehicles'] > 0 ? ($stats['total_active_vehicles'] * 100 / $stats['frontline_ready_vehicles']) : 0, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">— 0% vs previous period</div>
                    </div>
                </x-ui-card>
                
                <x-ui-card class="bg-white">
                    <div class="flex flex-col space-y-1">
                        <div class="text-sm font-medium text-gray-500">Conversion Rate</div>
                        <div class="text-2xl font-bold">{{ number_format($stats['frontline_ready_vehicles'] > 0 ? ($stats['frontline_ready_vehicles'] / $stats['total_active_vehicles'] * 100) : 0, 2) }}%</div>
                        <div class="text-xs text-gray-500 mt-1">— 0% vs previous period</div>
                    </div>
                </x-ui-card>
            </div>
            
            <!-- Tabs -->
            <div class="mb-6 border-b">
                <div class="flex space-x-6">
                    <button class="pb-2 text-sm font-medium border-b-2 border-black">Sales Overview</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Top Products</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Customer Insights</button>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Revenue & Orders Chart -->
                <div class="lg:col-span-2">
                    <x-ui-card>
                        <div class="flex flex-col space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium">Revenue & Orders</h3>
                                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->subMonth()->format('M d, Y') }} - {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 rounded-full bg-black"></div>
                                        <span class="text-sm">Revenue</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                                        <span class="text-sm">Orders</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chart placeholder -->
                            <div class="h-[350px] mt-4">
                                <x-ui.chart 
                                    type="line" 
                                    id="revenue-chart"
                                    :data="$chartData"
                                    :options="[
                                        'responsive' => true,
                                        'maintainAspectRatio' => false,
                                        'plugins' => [
                                            'legend' => [
                                                'display' => false
                                            ],
                                            'tooltip' => [
                                                'mode' => 'index',
                                                'intersect' => false
                                            ]
                                        ],
                                        'scales' => [
                                            'y' => [
                                                'beginAtZero' => true,
                                                'grid' => [
                                                    'drawBorder' => false
                                                ]
                                            ],
                                            'x' => [
                                                'grid' => [
                                                    'display' => false,
                                                    'drawBorder' => false
                                                ]
                                            ]
                                        ]
                                    ]"
                                    height="350px"
                                />
                            </div>
                        </div>
                    </x-ui-card>
                </div>
                
                <!-- Payment Methods -->
                <div>
                    <x-ui-card>
                        <div class="flex flex-col h-full">
                            <h3 class="text-lg font-medium mb-4">Payment Methods</h3>
                            
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center py-8">
                                    <div class="flex justify-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" x2="21" y1="9" y2="9"/><path d="M14 16H8"/></svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No payment data available for this period.</p>
                                </div>
                            </div>
                        </div>
                    </x-ui-card>
                </div>
            </div>
            
            <!-- Vehicle Stage Pipeline -->
            <div class="mt-6">
                <x-ui-card>
                    <x-slot name="title">Reconditioning Pipeline</x-slot>
                    
                    <div class="overflow-x-auto">
                        <div class="flex gap-2 py-2 whitespace-nowrap min-w-full pb-4">
                            @foreach($stages as $stage)
                                @php
                                    $count = $stageCounts[$stage->slug] ?? 0;
                                    $stageColor = match($stage->slug) {
                                        'intake' => 'border-gray-400 bg-gray-50',
                                        'mechanical', 'exterior', 'interior', 'tires_brakes' => 'border-yellow-400 bg-yellow-50',
                                        'test_drive', 'features', 'detail', 'walkaround', 'photos' => 'border-blue-400 bg-blue-50',
                                        'arbitration' => 'border-red-400 bg-red-50',
                                        'frontline' => 'border-green-400 bg-green-50',
                                        default => 'border-gray-400 bg-gray-50'
                                    };
                                @endphp
                                
                                <div class="min-w-[150px] flex-1 border rounded-md shadow-sm p-3 {{ $stageColor }}">
                                    <div class="font-medium text-center mb-1">{{ $stage->name }}</div>
                                    <div class="text-2xl font-bold text-center mb-2">{{ $count }}</div>
                                    <div class="text-xs text-center">
                                        <a href="{{ route('vehicles.index', ['stage' => $stage->slug]) }}" class="text-blue-600 hover:text-blue-800">
                                            View Vehicles
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-ui-card>
            </div>
            
            <!-- Recent Tasks and Vehicles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <x-ui-card>
                    <x-slot name="title">Recent Tasks</x-slot>
                    
                    @if(count($recentTasks) > 0)
                        <div class="space-y-4">
                            @foreach($recentTasks as $task)
                                <div class="p-3 border rounded-md {{ $task->isOverdue() ? 'bg-red-50 border-red-200' : 'bg-white' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $task->title }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                @if($task->vehicle)
                                                    <a href="{{ route('vehicles.show', $task->vehicle) }}" class="hover:underline">
                                                        {{ $task->vehicle->year }} {{ $task->vehicle->make }} {{ $task->vehicle->model }}
                                                    </a>
                                                @else
                                                    No Vehicle
                                                @endif
                                                • Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No Due Date' }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($task->status === 'completed')
                                                <x-ui-badge variant="success">Completed</x-ui-badge>
                                            @elseif($task->status === 'in_progress')
                                                <x-ui-badge variant="warning">In Progress</x-ui-badge>
                                            @elseif($task->status === 'pending')
                                                <x-ui-badge variant="info">Pending</x-ui-badge>
                                            @elseif($task->status === 'blocked')
                                                <x-ui-badge variant="danger">Blocked</x-ui-badge>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All Tasks
                            </a>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-6">
                            No tasks found
                        </div>
                    @endif
                </x-ui-card>
                
                <!-- Recently Added Vehicles -->
                <x-ui-card>
                    <x-slot name="title">Recently Added Vehicles</x-slot>
                    
                    @if(count($recentVehicles) > 0)
                        <div class="space-y-4">
                            @foreach($recentVehicles as $vehicle)
                                <div class="p-3 border rounded-md bg-white">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-medium">
                                                <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                VIN: {{ $vehicle->vin }} • Added: {{ $vehicle->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div>
                                            @php 
                                                $stageBadgeColors = [
                                                    'intake' => 'gray',
                                                    'test_drive' => 'info',
                                                    'arbitration' => 'danger',
                                                    'mechanical' => 'warning',
                                                    'exterior' => 'warning',
                                                    'interior' => 'warning',
                                                    'features' => 'info',
                                                    'tires_brakes' => 'warning',
                                                    'detail' => 'info',
                                                    'walkaround' => 'info',
                                                    'photos' => 'info',
                                                    'frontline' => 'success',
                                                ];
                                                $stageColor = $stageBadgeColors[$vehicle->current_stage] ?? 'default';
                                            @endphp
                                            <x-ui-badge :variant="$stageColor">
                                                {{ $stages->firstWhere('slug', $vehicle->current_stage)->name ?? 'Unknown' }}
                                            </x-ui-badge>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('vehicles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All Vehicles
                            </a>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-6">
                            No vehicles found
                        </div>
                    @endif
                </x-ui-card>
            </div>
        </div>
    </div>
</x-app-layout>
