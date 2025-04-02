<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: true }" class="min-h-screen bg-background">
            <div class="flex">
                <!-- Mobile sidebar backdrop -->
                <div 
                    x-show="sidebarOpen" 
                    @click="sidebarOpen = false" 
                    class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm transition-opacity lg:hidden">
                </div>

                <!-- Sidebar -->
                <div 
                    x-show="sidebarOpen" 
                    class="fixed inset-y-0 left-0 z-30 w-64 transform transition duration-300 lg:relative lg:translate-x-0" 
                    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                    <x-sidebar />
                </div>

                <!-- Content -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Top navigation -->
                    <div class="bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 border-b lg:hidden">
                        <div class="flex items-center justify-between h-16 px-4">
                            <button @click="sidebarOpen = true" class="text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <a href="{{ route('dashboard') }}" class="flex items-center">
                                <x-application-logo class="block h-9 w-auto fill-current text-foreground" />
                                <span class="ml-2 text-lg font-medium text-foreground">TrevinosAuto</span>
                            </a>
                            <div></div> <!-- Empty div for flex spacing -->
                        </div>
                    </div>

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="flex h-16 items-center justify-between">
                                    <!-- Left side - Header Title -->
                                    <div class="flex-1">
                                        {{ $header }}
                                    </div>

                                    <!-- Right side - User Menu -->
                                    <div class="flex items-center gap-4 pl-3">
                                        <!-- Notifications Dropdown -->
                                        <x-dropdown align="right" width="96">
                                            <x-slot name="trigger">
                                                <button class="relative inline-flex h-9 w-9 items-center justify-center rounded-md bg-background hover:bg-accent text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                                    </svg>
                                                    @if(auth()->user()->unread_notifications_count > 0)
                                                        <span class="absolute top-0 right-0 h-2.5 w-2.5 rounded-full bg-destructive"></span>
                                                    @endif
                                                </button>
                                            </x-slot>

                                            <x-slot name="content">
                                                <div class="p-2">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <h3 class="text-sm font-medium">Notifications</h3>
                                                        @if(auth()->user()->unread_notifications_count > 0)
                                                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="flex">
                                                                @csrf
                                                                <button type="submit" class="text-xs text-primary hover:text-primary/80 transition-colors">
                                                                    Mark all as read
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>

                                                    <div class="space-y-1 max-h-[400px] overflow-y-auto">
                                                        @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                                                            <div class="p-2 hover:bg-accent rounded-md transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                                                                <div class="flex items-start gap-3">
                                                                    <!-- Icon based on notification type -->
                                                                    <div class="flex-shrink-0 mt-0.5">
                                                                        @switch($notification->type)
                                                                            @case('App\Notifications\VehicleAssigned')
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                                                                                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.3 16 9 16 9s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 11v4c0 .6.4 1 1 1h2"/>
                                                                                    <circle cx="7" cy="17" r="2"/>
                                                                                    <circle cx="17" cy="17" r="2"/>
                                                                                </svg>
                                                                                @break
                                                                            @case('App\Notifications\InspectionCompleted')
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                                                                                    <path d="M20 6 9 17l-5-5"/>
                                                                                </svg>
                                                                                @break
                                                                            @default
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                                                                    <circle cx="12" cy="12" r="10"/>
                                                                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                                                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                                                                </svg>
                                                                        @endswitch
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-sm text-foreground">
                                                                            {{ $notification->data['message'] ?? 'No message available' }}
                                                                        </p>
                                                                        <p class="mt-1 text-xs text-muted-foreground">
                                                                            {{ $notification->created_at->diffForHumans() }}
                                                                        </p>
                                                                    </div>
                                                                    @unless($notification->read_at)
                                                                        <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="flex-shrink-0">
                                                                            @csrf
                                                                            <button type="submit" class="text-xs text-primary hover:text-primary/80 transition-colors">
                                                                                Mark as read
                                                                            </button>
                                                                        </form>
                                                                    @endunless
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="py-6 text-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 text-muted-foreground/60">
                                                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                                                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                                                </svg>
                                                                <p class="text-sm text-muted-foreground">No notifications yet</p>
                                                            </div>
                                                        @endforelse
                                                    </div>

                                                    @if(auth()->user()->notifications->count() > 5)
                                                        <div class="mt-4 text-center">
                                                            <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-1 text-sm text-primary hover:text-primary/80 transition-colors">
                                                                View all notifications
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="m9 18 6-6-6-6"/>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </x-slot>
                                        </x-dropdown>

                                        <!-- User Dropdown -->
                                        <x-dropdown align="right" width="56">
                                            <x-slot name="trigger">
                                                <button class="inline-flex items-center gap-3 px-2 py-1.5 rounded-md bg-background hover:bg-accent text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                    <!-- Avatar -->
                                                    <div class="relative h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-primary">
                                                            {{ substr(Auth::user()->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-col items-start">
                                                        <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                                        <span class="text-xs text-muted-foreground">{{ Auth::user()->roles->first()?->name ?? 'User' }}</span>
                                                    </div>
                                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </x-slot>

                                            <x-slot name="content">
                                                <div class="p-2">
                                                    <!-- User Info -->
                                                    <div class="px-2 py-1.5">
                                                        <p class="text-sm font-medium text-foreground">{{ Auth::user()->email }}</p>
                                                        <p class="text-xs text-muted-foreground mt-0.5">Joined {{ Auth::user()->created_at->format('M Y') }}</p>
                                                    </div>
                                                    
                                                    <div class="my-1 h-px bg-accent"></div>

                                                    <!-- Menu Items -->
                                                    <nav class="space-y-1">
                                                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 px-2 py-1.5 hover:bg-accent rounded-md transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                                                <circle cx="12" cy="7" r="4"/>
                                                            </svg>
                                                            {{ __('Profile') }}
                                                        </x-dropdown-link>

                                                        @role('admin')
                                                        <x-dropdown-link :href="route('admin.settings.index')" class="flex items-center gap-2 px-2 py-1.5 hover:bg-accent rounded-md transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg>
                                                            {{ __('Settings') }}
                                                        </x-dropdown-link>
                                                        @endrole

                                                        <div class="my-1 h-px bg-accent"></div>

                                                        <!-- Logout -->
                                                        <form method="POST" action="{{ route('logout') }}">
                                                            @csrf
                                                            <x-dropdown-link :href="route('logout')"
                                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                                class="flex items-center gap-2 px-2 py-1.5 text-destructive hover:bg-destructive/10 rounded-md transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                                                    <polyline points="16 17 21 12 16 7"/>
                                                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                                                </svg>
                                                                {{ __('Log Out') }}
                                                            </x-dropdown-link>
                                                        </form>
                                                    </nav>
                                                </div>
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                </div>
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="flex-1 overflow-y-auto bg-background">
                        <div class="py-6">
                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        </div>
        
        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>
