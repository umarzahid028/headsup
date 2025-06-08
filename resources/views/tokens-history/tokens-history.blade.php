<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="mt-10 px-6">
        <h3 class="text-3xl font-bold mb-8 text-gray-900 tracking-wide">Customer Tokens</h3>

        <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-300">
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @php
                            $headers = ['Token No', 'Status', 'Assigned At', 'Completed At', 'Duration'];
                        @endphp
                        @foreach ($headers as $header)
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider select-none">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tokens as $token)
                        @php
                            $assigned = $token->assigned_at ? \Carbon\Carbon::parse($token->assigned_at) : null;
                            $completed = $token->completed_at ? \Carbon\Carbon::parse($token->completed_at) : null;

                            $assignedTime = $assigned ? $assigned->format('h:i:s A') : '-';
                            $completedTime = $completed ? $completed->format('h:i:s A') : '-';

                            $duration = '-';
                            if ($assigned && $completed) {
                                $totalSeconds = $assigned->diffInSeconds($completed);
                                $h = floor($totalSeconds / 3600);
                                $m = floor(($totalSeconds % 3600) / 60);
                                $s = $totalSeconds % 60;
                                $duration = "{$h}h {$m}m {$s}s";
                            }

                            $badgeClass = $token->status === 'completed' 
                                ? 'bg-green-100 text-green-800'
                                : 'bg-gray-100 text-gray-700';
                        @endphp

                        <tr class="hover:bg-gray-50 text-center">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $token->serial_number }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ ucfirst($token->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $assignedTime }}</td>
                            <td class="px-4 py-3">{{ $completedTime }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $duration }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">No completed tokens available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-app-layout>
