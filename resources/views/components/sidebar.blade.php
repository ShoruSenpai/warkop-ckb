@php
    $user = auth()->user();
    $userRole = $user->role ?? 'employee';

    $menus = [
        'MENU UTAMA' => [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-chart-pie', 'badge' => 0, 'roles' => ['owner', 'admin']],
            ['name' => 'Master Produk', 'route' => 'product.index', 'icon' => 'fas fa-box-open', 'badge' => 0, 'roles' => ['owner', 'admin']],
            ['name' => 'Master Bahan Baku', 'route' => 'raw-material.index', 'icon' => 'fas fa-cubes', 'badge' => 3, 'roles' => ['owner', 'admin']],
            ['name' => 'Pembelian Supplier', 'route' => 'supplier.index', 'icon' => 'fas fa-truck-loading', 'badge' => 0, 'roles' => ['owner', 'admin']],
            ['name' => 'Laporan', 'route' => 'report.index', 'icon' => 'fas fa-file-invoice-dollar', 'badge' => 0, 'roles' => ['owner', 'admin']],
        ],
        'SISTEM' => [
            ['name' => 'Pengaturan', 'route' => 'settings', 'icon' => 'fas fa-cog', 'badge' => 0, 'roles' => ['owner', 'admin']],
            // Manajemen User eksklusif untuk Owner
            ['name' => 'Manajemen User', 'route' => 'users.index', 'icon' => 'fas fa-users-cog', 'badge' => 0, 'roles' => ['owner']],
        ]
    ];
@endphp

<aside class="w-64 bg-ckb-primary text-ckb-bg flex flex-col h-full shrink-0">
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <div class="w-8 h-8 bg-white rounded flex items-center justify-center text-ckb-primary font-bold mr-3">CKB</div>
        <div>
            <h1 class="text-sm font-bold leading-tight">Warkop Cak Kebo</h1>
            <p class="text-[10px] text-gray-400 tracking-wider">ADMIN PANEL</p>
        </div>
    </div>

    <div class="px-6 py-4 flex items-center border-b border-white/10">
        <!-- Menggunakan Auth::user() untuk memanggil relasi employee -->
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->username ?? 'User') }}&background=75584D&color=fff" class="w-10 h-10 rounded-full mr-3">
        <div>
            <p class="text-sm font-semibold">{{ $user->employee->full_name ?? $user->username ?? 'Admin' }}</p>
            <span class="px-2 py-0.5 bg-white/10 rounded text-[10px] uppercase">{{ $userRole }}</span>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        @foreach($menus as $category => $items)
            @php
                // Filter menu sesuai role
                $visibleItems = array_filter($items, function($item) use ($userRole) {
                    return in_array($userRole, $item['roles']);
                });
            @endphp

            @if(count($visibleItems) > 0)
                <div class="mb-6">
                    <p class="px-6 text-xs font-semibold text-gray-400 mb-2 tracking-wider">{{ $category }}</p>
                    <ul>
                        @foreach($visibleItems as $item)
                            @php
                                $isActive = Route::has($item['route']) && request()->routeIs($item['route']);
                                $routeUrl = Route::has($item['route']) ? route($item['route']) : '#';
                            @endphp
                            <li>
                                <a href="{{ $routeUrl }}"
                                   class="flex items-center px-6 py-2.5 text-sm transition-colors {{ $isActive ? 'bg-white/10 border-l-4 border-ckb-bg font-medium' : 'text-gray-300 hover:bg-white/5 border-l-4 border-transparent' }}">
                                    <i class="{{ $item['icon'] }} w-5 text-center mr-3 text-lg {{ $isActive ? 'text-white' : 'text-gray-400' }}"></i>
                                    <span class="flex-1">{{ $item['name'] }}</span>
                                    @if($item['badge'] > 0)
                                        <span class="bg-ckb-warning text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </nav>
</aside>
