<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6 px-4">
            <!-- Stats Grid -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <!-- Total Sales Person -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary/10">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m4-3.13a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 100-8 4 4 0 000 8z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Sales Person</p>
                            <p class="text-lg font-semibold text-foreground">{{ $queues ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">
                                @if(($queueGrowth ?? 0) > 0)
                                    <span class="text-emerald-500">+{{ $queueGrowth }}%</span>
                                @else
                                    <span class="text-destructive">{{ $queueGrowth }}%</span>
                                @endif
                                from last month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500/10">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m4-3.13a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 100-8 4 4 0 000 8z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Customers</p>
                            <p class="text-lg font-semibold text-foreground">{{ $customer ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">Reflects all active customer records</p>
                        </div>
                    </div>
                </div>

                <!-- Total Appointments -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500/10">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Appointments</p>
                            <p class="text-lg font-semibold text-foreground">{{ $appointment ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">Includes all scheduled sessions</p>
                        </div>
                    </div>
                </div>

                <!-- Sold Customers -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500/10">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18 8a6 6 0 00-12 0v2a3 3 0 003 3h1m6-5v5a2 2 0 002 2h1a3 3 0 003-3v-1a6 6 0 00-6-6zM8 14h.01M8 14a4 4 0 014-4h4a2 2 0 012 2v2a4 4 0 01-4 4h-4a4 4 0 01-4-4z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Sold Customers</p>
                            <p class="text-lg font-semibold text-foreground">{{ $customerdetail ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">Marked as 'Sold!' in system</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-2">
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-foreground">Sales Person Growth</h3>
                    <div class="h-64" id="salesPersonChart"></div>
                </div>

                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-foreground">Customer Count Over Time</h3>
                    <div class="h-64" id="customerChart"></div>
                </div>

                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-foreground">Appointments Over Time</h3>
                    <div class="h-64" id="appointmentChart"></div>
                </div>

                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-foreground">Sold Customers Ratio</h3>
                    <div class="h-64" id="soldCustomerChart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            new ApexCharts(document.querySelector("#salesPersonChart"), {
                series: [{ name: 'Sales Persons', data: {!! json_encode($salesGrowthChart ?? [10, 20, 30, 40, 50]) !!} }],
                chart: { type: 'bar', height: 250, background: 'transparent', foreColor: '#a1a1aa' },
                xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May'], labels: { style: { colors: '#a1a1aa' } } },
                colors: ['#0ea5e9'], theme: { mode: 'dark' }
            }).render();

            new ApexCharts(document.querySelector("#customerChart"), {
                series: [{ name: 'Customers', data: {!! json_encode($customerChart ?? [5, 15, 25, 35, 45]) !!} }],
                chart: { type: 'line', height: 250, background: 'transparent', foreColor: '#a1a1aa' },
                stroke: { curve: 'smooth', colors: ['#10b981'] },
                xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May'], labels: { style: { colors: '#a1a1aa' } } },
                theme: { mode: 'dark' }
            }).render();

            new ApexCharts(document.querySelector("#appointmentChart"), {
                series: [{ name: 'Appointments', data: {!! json_encode($appointmentChart ?? [2, 10, 15, 30, 40]) !!} }],
                chart: { type: 'area', height: 250, background: 'transparent', foreColor: '#a1a1aa' },
                fill: { gradient: { opacityFrom: 0.4, opacityTo: 0.1 } },
                stroke: { curve: 'smooth', colors: ['#8b5cf6'] },
                xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May'], labels: { style: { colors: '#a1a1aa' } } },
                theme: { mode: 'dark' }
            }).render();

            new ApexCharts(document.querySelector("#soldCustomerChart"), {
                series: Object.values({!! json_encode($soldChart ?? ['Sold' => 60, 'Unsold' => 40]) !!}),
                chart: { type: 'donut', height: 250, background: 'transparent', foreColor: '#a1a1aa' },
                labels: Object.keys({!! json_encode($soldChart ?? ['Sold' => 60, 'Unsold' => 40]) !!}),
                colors: ['#22c55e', '#ef4444'],
                legend: { position: 'bottom', labels: { colors: '#a1a1aa' } },
                theme: { mode: 'dark' }
            }).render();
        </script>
    @endpush
</x-app-layout>
