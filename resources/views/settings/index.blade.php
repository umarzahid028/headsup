<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Settings Navigation Sidebar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                        <nav class="space-y-1">
                            <a href="#user-preferences" class="flex items-center px-3 py-2 text-sm font-medium text-indigo-600 rounded-md bg-indigo-50" aria-current="page">
                                <svg class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>User Preferences</span>
                            </a>

                            <a href="#notification-settings" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                <svg class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span>Notification Settings</span>
                            </a>

                            <a href="#app-settings" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                <svg class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>App Settings</span>
                            </a>

                            <a href="#integrations" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                <svg class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Integrations</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Main Settings Content -->
                <div class="col-span-2">
                    <!-- User Preferences Section -->
                    <div id="user-preferences" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Preferences</h3>
                            
                            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                                @csrf
                                @method('patch')
                                
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                                
                                <div>
                                    <x-input-label for="theme" :value="__('Theme')" />
                                    <select id="theme" name="theme" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="light" @if(($settings['theme'] ?? '') === 'light') selected @endif>Light</option>
                                        <option value="dark" @if(($settings['theme'] ?? '') === 'dark') selected @endif>Dark</option>
                                        <option value="system" @if(($settings['theme'] ?? '') === 'system') selected @endif>System Default</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <x-input-label for="default_view" :value="__('Default View')" />
                                    <select id="default_view" name="default_view" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="list" @if(($settings['default_view'] ?? '') === 'list') selected @endif>List View</option>
                                        <option value="grid" @if(($settings['default_view'] ?? '') === 'grid') selected @endif>Grid View</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>

                                    @if (session('status') === 'profile-updated')
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition
                                            x-init="setTimeout(() => show = false, 2000)"
                                            class="text-sm text-gray-600"
                                        >{{ __('Saved.') }}</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notification Settings Section -->
                    <div id="notification-settings" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>
                            
                            <form method="POST" action="#" class="space-y-6">
                                @csrf
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="email_notifications" name="email_notifications" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="email_notifications" class="font-medium text-gray-700">Email Notifications</label>
                                            <p class="text-gray-500">Receive system notifications via email.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="task_reminders" name="task_reminders" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="task_reminders" class="font-medium text-gray-700">Task Reminders</label>
                                            <p class="text-gray-500">Get reminders for upcoming tasks and deadlines.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="we_owe_updates" name="we_owe_updates" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="we_owe_updates" class="font-medium text-gray-700">We-Owe Updates</label>
                                            <p class="text-gray-500">Get notified about We-Owe item status changes.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save Notification Preferences') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- App Settings Section -->
                    <div id="app-settings" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">App Settings</h3>
                            
                            <p class="text-gray-500 mb-4">Configure global application settings. Some changes may require administrator privileges.</p>
                            
                            <div class="flex">
                                <a href="{{ route('ftp-import.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                    FTP Import Settings
                                </a>
                                
                                <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    System Configuration
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 