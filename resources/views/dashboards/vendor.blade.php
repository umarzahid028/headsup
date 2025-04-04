@php
// Dummy data for vendor dashboard
$dummyEstimatesData = collect([
    ['date' => 'Jan', 'total' => 25000, 'count' => 10],
    ['date' => 'Feb', 'total' => 35000, 'count' => 15],
    ['date' => 'Mar', 'total' => 28000, 'count' => 12],
    ['date' => 'Apr', 'total' => 42000, 'count' => 18],
    ['date' => 'May', 'total' => 38000, 'count' => 16],
    ['date' => 'Jun', 'total' => 45000, 'count' => 20],
]);

$dummyEstimateStats = [
    'total' => 91,
    'pending' => 25,
    'approved' => 45,
    'rejected' => 21
];
@endphp

<x-app-layout>


    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold tracking-tight mb-6">Vendor Dashboard</h2>

        <!-- Stats Overview -->
        <div class="grid gap-4 md:grid-cols-4 mb-6">
            <!-- Total Estimates -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Total Estimates</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="h-4 w-4 text-muted-foreground">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold">{{ $dummyEstimateStats['total'] }}</div>
                <p class="text-xs text-muted-foreground">Total estimates submitted</p>
            </div>

            <!-- Pending Estimates -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Pending</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="h-4 w-4 text-muted-foreground">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="text-2xl font-bold">{{ $dummyEstimateStats['pending'] }}</div>
                <p class="text-xs text-muted-foreground">Awaiting approval</p>
            </div>

            <!-- Approved Estimates -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Approved</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="h-4 w-4 text-muted-foreground">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold">{{ $dummyEstimateStats['approved'] }}</div>
                <p class="text-xs text-muted-foreground">Approved estimates</p>
            </div>

            <!-- Monthly Total -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Monthly Total</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="h-4 w-4 text-muted-foreground">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="text-2xl font-bold">${{ number_format($dummyEstimatesData->sum('total'), 2) }}</div>
                <p class="text-xs text-muted-foreground">This month's total</p>
            </div>
        </div>

        <!-- Recent Estimates -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
            <h3 class="font-semibold text-lg mb-4">Recent Estimates</h3>
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t"></span>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-background px-2 text-muted-foreground">Last 30 days</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 