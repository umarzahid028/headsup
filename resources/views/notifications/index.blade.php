<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-foreground leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-background shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Header with Mark All as Read button -->
                @if($notifications->where('read_at', null)->count() > 0)
                    <div class="flex justify-end mb-4">
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                {{ __('Mark all as read') }}
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Notifications List -->
                <div class="space-y-4">
                    @forelse($notifications as $notification)
                        <div class="flex items-start gap-4 p-4 {{ $notification->read_at ? 'bg-background' : 'bg-accent' }} rounded-lg border border-border">
                            <!-- Icon based on notification type -->
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('App\Notifications\VehicleAssigned')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                                            <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.3 16 9 16 9s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 11v4c0 .6.4 1 1 1h2"/>
                                            <circle cx="7" cy="17" r="2"/>
                                            <circle cx="17" cy="17" r="2"/>
                                        </svg>
                                        @break
                                    @case('App\Notifications\InspectionCompleted')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                                            <path d="M20 6 9 17l-5-5"/>
                                        </svg>
                                        @break
                                    @default
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="16" x2="12" y2="12"/>
                                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                                        </svg>
                                @endswitch
                            </div>

                            <!-- Notification Content -->
                            <div class="flex-1">
                                <p class="text-sm text-foreground">
                                    {{ $notification->data['message'] ?? 'No message available' }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                @if($notification->data['action_url'] ?? false)
                                    <a href="{{ $notification->data['action_url'] }}" class="mt-2 inline-flex items-center text-sm text-primary hover:text-primary/80">
                                        {{ $notification->data['action_text'] ?? 'View Details' }}
                                        <svg class="ml-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @endif
                            </div>

                            <!-- Mark as Read Button -->
                            @unless($notification->read_at)
                                <div class="flex-shrink-0">
                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-sm text-primary hover:text-primary/80">
                                            Mark as read
                                        </button>
                                    </form>
                                </div>
                            @endunless
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-foreground">No notifications</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                You don't have any notifications at the moment.
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 