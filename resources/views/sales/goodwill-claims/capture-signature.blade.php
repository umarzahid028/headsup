<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Capture Customer Signature') }}
            </h2>
            <a href="{{ route('sales.goodwill-claims.show', $claim) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Back to Claim') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold mb-2">Goodwill Claim Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm"><span class="font-medium">Customer:</span> {{ $claim->customer_name }}</p>
                                    <p class="text-sm"><span class="font-medium">Phone:</span> {{ $claim->customer_phone }}</p>
                                    @if($claim->customer_email)
                                        <p class="text-sm"><span class="font-medium">Email:</span> {{ $claim->customer_email }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($claim->vehicle)
                                        <p class="text-sm"><span class="font-medium">Vehicle:</span> {{ $claim->vehicle->year }} {{ $claim->vehicle->make }} {{ $claim->vehicle->model }}</p>
                                        <p class="text-sm"><span class="font-medium">VIN:</span> {{ $claim->vehicle->vin }}</p>
                                    @endif
                                    <p class="text-sm"><span class="font-medium">Date:</span> {{ now()->format('F j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold mb-2">Issue Description</h3>
                            <p class="text-sm whitespace-pre-wrap">{{ $claim->issue_description }}</p>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold mb-2">Requested Resolution</h3>
                            <p class="text-sm whitespace-pre-wrap">{{ $claim->requested_resolution }}</p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg text-sm mb-6">
                            <p class="font-semibold mb-2">Customer Agreement</p>
                            <p>By signing below, I acknowledge and consent to the terms of this goodwill claim. I understand that this represents a goodwill gesture by the dealership and does not constitute an admission of liability or warranty.</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Signature Capture</h3>
                            <p class="text-sm text-gray-500 mb-4">Please have the customer sign below:</p>
                            
                            <form id="signatureForm" action="{{ route('sales.goodwill-claims.signature.store', $claim) }}" method="POST">
                                @csrf
                                <input type="hidden" name="signature" id="signature" required>
                                
                                <div class="border border-gray-300 rounded-lg p-2 mb-4">
                                    <div id="signature-pad" class="w-full h-64 border border-gray-200 touch-none"></div>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <button type="button" id="clearButton" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        Clear Signature
                                    </button>
                                    
                                    <button type="submit" id="saveButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" disabled>
                                        Save Signature
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signature-pad');
            const signatureInput = document.getElementById('signature');
            const clearButton = document.getElementById('clearButton');
            const saveButton = document.getElementById('saveButton');
            const signatureForm = document.getElementById('signatureForm');
            
            // Initialize SignaturePad
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });
            
            // Resize canvas for better display
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear(); // Otherwise the canvas gets cleared
            }
            
            // Set canvas initial size
            resizeCanvas();
            
            // Add event listeners
            window.addEventListener('resize', resizeCanvas);
            
            // Clear the signature pad
            clearButton.addEventListener('click', function() {
                signaturePad.clear();
                saveButton.disabled = true;
            });
            
            // Enable save button when signature is being drawn
            signaturePad.addEventListener('beginStroke', function() {
                saveButton.disabled = false;
            });
            
            // Handle form submission
            signatureForm.addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Please provide a signature first.');
                    return false;
                }
                
                // Get signature as data URL and store in hidden input
                const dataURL = signaturePad.toDataURL('image/png');
                signatureInput.value = dataURL;
                return true;
            });
        });
    </script>
    @endpush
</x-app-layout> 