@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="space-y-1">
            <h2 class="text-2xl font-semibold tracking-tight">Roles & Permissions</h2>
            <p class="text-sm text-muted-foreground">View system roles and their permissions.</p>
        </div>
    </div>

    <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-4 sm:mx-6 lg:mx-8">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Name</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Permissions</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Users</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @foreach($roles as $role)
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium">{{ $role->name }}</td>
                            <td class="p-4 align-middle">
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                        @php
                                            $actionPermissions = $role->permissions->filter(function($permission) use ($action) {
                                                return explode(' ', $permission->name)[0] === $action;
                                            });
                                        @endphp
                                        @if($actionPermissions->count() > 0)
                                            <div class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                                {{ $action === 'view' ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10' : 
                                                   ($action === 'create' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 
                                                   ($action === 'edit' ? 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20' : 
                                                   ($action === 'delete' ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10' : 
                                                   'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20'))) }}">
                                                {{ $action }} ({{ $actionPermissions->count() }})
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <span class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/20">
                                    {{ $role->users_count ?? 0 }} users
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($roles->isEmpty())
            <div class="flex items-center justify-center p-8 text-center">
                <div class="space-y-2">
                    <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-muted-foreground/60" />
                    <h3 class="text-lg font-medium">No roles found</h3>
                    <p class="text-sm text-muted-foreground">The system has no roles configured.</p>
                </div>
            </div>
        @endif
    </div>

    @if($roles->hasPages())
        <div class="px-4 sm:px-6 lg:px-8">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection 