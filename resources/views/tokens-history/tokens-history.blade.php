<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Customers') }}
        </h2>
    </x-slot>

    <div class="mt-10 px-6 space-y-6">


        @if ($customerSales->isEmpty())
            <div class="text-gray-500">No assigned customers found.</div>
        @else
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-black">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Assigned At</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Activities</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-left">
                        @foreach ($customerSales as $index => $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">{{ $sale->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y h:i A') }}</td>
                                <td class="px-6 py-3">
                                    @foreach ($sale->process as $process)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">{{ $process }}</span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-3">
                                    @if ($sale->served_at)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Served</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Processing</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
