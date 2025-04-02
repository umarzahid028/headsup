<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Roles & Permissions</h2>
                <p class="text-sm text-muted-foreground">View system roles and their permissions.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
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
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{ $role->name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                            <span class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/20">
                                                {{ $permission->name }}
                                            </span>
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
</x-app-layout> 