<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Token History') }}
        </h2>
    </x-slot>

    <div class="mt-10 px-6 space-y-12">
        {{-- Completed Tokens --}}
        <div>
            <h3 class="text-2xl font-bold mb-4 ">Completed Tokens (Last 24 Hours)</h3>

            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-black ">
                        <tr>
                            @php
                                $headers = ['Token No', 'Status', 'Assigned At', 'Completed At', 'Duration'];
                            @endphp
                            @foreach ($headers as $header)
                                <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-center">
                        @php
                            $completedTokens = $tokens->where('status', 'completed');
                        @endphp

                        @forelse ($completedTokens as $token)
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
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $token->serial_number }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-800 text-white">
                                        {{ ucfirst($token->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $assignedTime }}</td>
                                <td class="px-4 py-3">{{ $completedTime }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $duration }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500">No completed tokens in last 24 hours.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Skipped Tokens --}}
        <div class="mt-4">
            <h3 class="text-2xl font-bold mb-4">Skipped Tokens (Last 24 Hours)</h3>

            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-black ">
                        <tr>
                            @foreach ($headers as $header)
                                <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-center">
                        @php
                            $skippedTokens = $tokens->where('status', 'skipped');
                        @endphp

                        @forelse ($skippedTokens as $token)
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
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $token->serial_number }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-800 text-white">
                                        {{ ucfirst($token->status) }}
                                    </span>
                                    
                                </td>
                                <td class="px-4 py-3">{{ $assignedTime }}</td>
                                <td class="px-4 py-3">{{ $completedTime }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $duration }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500">No skipped tokens in last 24 hours.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
