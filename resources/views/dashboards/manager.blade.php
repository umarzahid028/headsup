<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Manager Dashboard</h2>
                <p class="text-sm text-muted-foreground">View and manage your dealership's performance metrics.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6 p-4 sm:p-6 lg:p-8">
        <!-- Stats Overview -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Vehicle Stats -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Total Vehicles</h3>
                        <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $vehicleStats['total'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                +{{ $vehicleStats['in_stock'] }} in stock
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-wrench-screwdriver class="h-3 w-3"/>
                            {{ $vehicleStats['in_recon'] }} in recon
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-check-circle class="h-3 w-3"/>
                            {{ $vehicleStats['sold'] }} sold
                        </span>
                    </div>
                </div>
            </x-ui.card>

            <!-- Transport Stats -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Transports</h3>
                        <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $transportStats['pending'] + $transportStats['in_transit'] + $transportStats['delivered'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning/10 text-warning">
                                {{ $transportStats['pending'] }} pending
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="h-3 w-3"/>
                            {{ $transportStats['in_transit'] }} in transit
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-check-circle class="h-3 w-3"/>
                            {{ $transportStats['delivered'] }} delivered
                        </span>
                    </div>
                </div>
            </x-ui.card>

            <!-- Sales Overview -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Monthly Sales</h3>
                        <x-heroicon-o-currency-dollar class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">${{ number_format($salesData->sum('total'), 2) }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success">
                                {{ $salesData->sum('count') }} sales
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Monthly revenue from vehicle sales
                    </div>
                </div>
            </x-ui.card>

            <!-- Estimates Overview -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Monthly Estimates</h3>
                        <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">${{ number_format($estimatesData->sum('total'), 2) }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-info/10 text-info">
                                {{ $estimatesData->sum('count') }} estimates
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Total value of repair estimates
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout> 