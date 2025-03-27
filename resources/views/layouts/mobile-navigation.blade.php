<div>
    <!-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius -->
</div>

<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-responsive-nav-link>
    
    <div class="space-y-1">
        <x-responsive-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.index')">
            {{ __('Vehicles') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('vehicles.intake')" :active="request()->routeIs('vehicles.intake')">
            {{ __('Intake & Dispatch') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('vehicles.frontline.index')" :active="request()->routeIs('vehicles.frontline.*')">
            {{ __('Frontline Ready') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('recon.workflows.index')" :active="request()->routeIs('recon.*')">
            {{ __('2Recon Workflow') }}
        </x-responsive-nav-link>
    </div>
    
    <div class="space-y-1 pt-2 border-t border-gray-200">
        <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Post-Sale Management
        </p>
        <x-responsive-nav-link :href="route('post-sale.index')" :active="request()->routeIs('post-sale.index')">
            {{ __('Archived Vehicles') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('post-sale.we-owe-items')" :active="request()->routeIs('post-sale.we-owe-items')">
            {{ __('We-Owe Items') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('post-sale.goodwill-repairs')" :active="request()->routeIs('post-sale.goodwill-repairs')">
            {{ __('Goodwill Repairs') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('goodwill-repairs.create')" :active="request()->routeIs('goodwill-repairs.create')">
            {{ __('New Goodwill Repair') }}
        </x-responsive-nav-link>
    </div>
    
    <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index') || request()->routeIs('tasks.create') || request()->routeIs('tasks.edit')">
        {{ __('Tasks') }}
    </x-responsive-nav-link>
</div>
