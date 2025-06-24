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
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Sales Person</p>
                            <p class="text-lg font-semibold text-foreground">{{ $queues ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Customers</p>
                            <p class="text-lg font-semibold text-foreground">{{ $customer ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Appointments -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                       <div class="p-3 rounded-full bg-primary/10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Appointments</p>
                            <p class="text-lg font-semibold text-foreground">{{ $appointment ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sold Customers -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18 8a6 6 0 00-12 0v2a3 3 0 003 3h1m6-5v5a2 2 0 002 2h1a3 3 0 003-3v-1a6 6 0 00-6-6zM8 14h.01M8 14a4 4 0 014-4h4a2 2 0 012 2v2a4 4 0 01-4 4h-4a4 4 0 01-4-4z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Sold Customers</p>
                            <p class="text-lg font-semibold text-foreground">{{ $customerdetail ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid (without Sales Person Growth) -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-2">
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
            const monthLabels = {!! json_encode($months ?? []) !!};

            new ApexCharts(document.querySelector("#customerChart"), {
                series: [{
                    name: 'Customers',
                    data: {!! json_encode($customerChart ?? []) !!}
                }],
                chart: {
                    type: 'line',
                    height: 250,
                    background: 'transparent',
                    foreColor: '#a1a1aa'
                },
                stroke: {
                    curve: 'smooth',
                    colors: ['#10b981']
                },
                xaxis: {
                    categories: monthLabels,
                    labels: { style: { colors: '#a1a1aa' } }
                },
                theme: { mode: 'dark' }
            }).render();

            new ApexCharts(document.querySelector("#appointmentChart"), {
                series: [{
                    name: 'Appointments',
                    data: {!! json_encode($appointmentChart ?? []) !!}
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    background: 'transparent',
                    foreColor: '#a1a1aa'
                },
                fill: {
                    gradient: { opacityFrom: 0.4, opacityTo: 0.1 }
                },
                stroke: {
                    curve: 'smooth',
                    colors: ['#8b5cf6']
                },
                xaxis: {
                    categories: monthLabels,
                    labels: { style: { colors: '#a1a1aa' } }
                },
                theme: { mode: 'dark' }
            }).render();

            new ApexCharts(document.querySelector("#soldCustomerChart"), {
                series: Object.values({!! json_encode($soldChart ?? []) !!}),
                chart: {
                    type: 'donut',
                    height: 250,
                    background: 'transparent',
                    foreColor: '#a1a1aa'
                },
                labels: Object.keys({!! json_encode($soldChart ?? []) !!}),
                colors: ['#22c55e', '#ef4444'],
                legend: {
                    position: 'bottom',
                    labels: { colors: '#a1a1aa' }
                },
                theme: { mode: 'dark' }
            }).render();
        </script>
    @endpush
</x-app-layout>
