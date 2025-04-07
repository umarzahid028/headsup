<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Goodwill Claim Details
            </h2>
            <div class="flex space-x-4">
                @if($claim->status === 'pending')
                    <form action="{{ route('sales.goodwill-claims.update-status', $claim) }}" method="POST" class="flex space-x-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-text-input type="number" step="0.01" name="estimated_cost" placeholder="Estimated Cost" required
                                class="w-32" value="{{ old('estimated_cost', $claim->estimated_cost) }}" />
                            <x-input-error :messages="$errors->get('estimated_cost')" class="mt-2" />
                        </div>
                        <x-button name="status" value="approved" class="bg-green-600 hover:bg-green-500">
                            Approve Claim
                        </x-button>
                        <x-button name="status" value="rejected" class="bg-red-600 hover:bg-red-500">
                            Reject Claim
                        </x-button>
                    </form>
                @endif
                <a href="{{ route('sales.goodwill-claims.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Vehicle Information</h3>
                            <div class="space-y-2">
                                @if($claim->vehicle)
                                    <p><span class="font-medium">Stock Number:</span> {{ $claim->vehicle->stock_number }}</p>
                                    <p><span class="font-medium">Vehicle:</span> {{ $claim->vehicle->year }} {{ $claim->vehicle->make }} {{ $claim->vehicle->model }}</p>
                                    <p><span class="font-medium">VIN:</span> {{ $claim->vehicle->vin }}</p>
                                @else
                                    <p class="text-red-600">Vehicle information not available</p>
                                @endif
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $claim->customer_name }}</p>
                                <p><span class="font-medium">Phone:</span> {{ $claim->customer_phone }}</p>
                                <p><span class="font-medium">Email:</span> {{ $claim->customer_email }}</p>
                                <p><span class="font-medium">Customer Consent:</span> 
                                    <span class="px-2 py-1 rounded text-sm {{ $claim->customer_consent ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $claim->customer_consent ? 'Given' : 'Not Given' }}
                                    </span>
                                </p>
                                @if($claim->customer_consent && $claim->customer_consent_date)
                                    <p><span class="font-medium">Consent Date:</span> {{ $claim->customer_consent_date->format('M d, Y') }}</p>
                                @endif
                                
                                <!-- Customer Signature -->
                                @if($claim->hasSignature())
                                    <div class="mt-4">
                                        <p><span class="font-medium">Signature:</span> Captured on {{ $claim->signature_date->format('M d, Y') }}</p>
                                        <div class="mt-2 p-2 border border-gray-200 bg-white">
                                            <img src="data:image/png;base64,{{ $claim->customer_signature }}" 
                                                alt="Customer Signature" 
                                                class="max-h-24 max-w-full" />
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <a href="{{ route('sales.goodwill-claims.signature.show', $claim) }}" 
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                            Capture Signature In Person
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Issue Description -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Issue Description</h3>
                            <p class="whitespace-pre-wrap">{{ $claim->issue_description }}</p>
                        </div>

                        <!-- Requested Resolution -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Requested Resolution</h3>
                            <p class="whitespace-pre-wrap">{{ $claim->requested_resolution }}</p>
                        </div>

                        <!-- Claim Status -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Claim Status</h3>
                            <div class="space-y-4">
                                <p>
                                    <span class="font-medium">Status:</span>
                                    <span class="ml-2 px-2 py-1 rounded text-sm
                                        @if($claim->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($claim->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($claim->status) }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Created By:</span> {{ $claim->createdBy ? $claim->createdBy->name : 'Unknown' }}</p>
                                <p><span class="font-medium">Created At:</span> {{ $claim->created_at ? $claim->created_at->format('M d, Y H:i A') : 'N/A' }}</p>
                                
                                @if($claim->status !== 'pending')
                                    @if($claim->approvedBy)
                                        <p><span class="font-medium">Decision By:</span> {{ $claim->approvedBy->name }}</p>
                                        <p><span class="font-medium">Decision At:</span> {{ $claim->approved_at ? $claim->approved_at->format('M d, Y H:i A') : 'N/A' }}</p>
                                    @else
                                        <p><span class="font-medium">Decision By:</span> Unknown</p>
                                    @endif
                                    @if($claim->approval_notes)
                                        <p><span class="font-medium">Decision Notes:</span></p>
                                        <p class="whitespace-pre-wrap">{{ $claim->approval_notes }}</p>
                                    @endif
                                @endif

                                @if($claim->estimated_cost)
                                    <p><span class="font-medium">Estimated Cost:</span> ${{ number_format($claim->estimated_cost, 2) }}</p>
                                @endif
                                @if($claim->actual_cost)
                                    <p><span class="font-medium">Actual Cost:</span> ${{ number_format($claim->actual_cost, 2) }}</p>
                                @endif
                            </div>
                        </div>

                        @if($claim->salesIssue)
                        <!-- Related Sales Issue -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Related Sales Issue</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Issue Type:</span> {{ $claim->salesIssue->issue_type }}</p>
                                <p><span class="font-medium">Status:</span>
                                    <span class="px-2 py-1 rounded text-sm
                                        @if($claim->salesIssue->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($claim->salesIssue->status) }}
                                    </span>
                                </p>
                                <a href="{{ route('sales.issues.show', $claim->salesIssue) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    View Issue Details
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</x-app-layout> 