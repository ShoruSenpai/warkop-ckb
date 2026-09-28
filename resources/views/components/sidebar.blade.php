@php
    $user = auth()->user();
    $userRole = $user->role ?? "employee";

    $menus = [
        "MAIN MENU" => [
            [
                "name" => "Dashboard",
                "route" => "dashboard",
                "active" => "dashboard",
                "icon" => "fas fa-chart-pie",
                "badge" => 0,
                "roles" => ["owner", "admin"],
            ],

            [
                "name" => "Product Management",
                "route" => "products.index",
                "active" => "products.*",
                "icon" => "fas fa-box-open",
                "badge" => 0,
                "roles" => ["owner", "admin"],
            ],

            [
                "name" => "Raw Materials",
                "route" => "raw-material.index",
                "active" => "raw-material.*",
                "icon" => "fas fa-cubes",
                "badge" => 0,
                "roles" => ["owner", "admin"],
            ],

            [
                "name" => "Purchases",
                "route" => "supplier.index",
                "active" => "supplier.*",
                "icon" => "fas fa-truck-loading",
                "badge" => 0,
                "roles" => ["owner", "admin"],
            ],

            [
                "name" => "Laporan",
                "type" => "dropdown",
                "icon" => "fas fa-file-invoice-dollar",
                "roles" => ["owner", "admin"],

                "children" => [
                    [
                        "name" => "Riwayat Transaksi",
                        "route" => "reports.transactions",
                        "active" => "reports.transactions*",
                    ],

                    [
                        "name" => "Laporan Penjualan",
                        "route" => "reports.sales",
                        "active" => "reports.sales*",
                    ],

                    [
                        "name" => "Laporan Pembelian",
                        "route" => "reports.purchases",
                        "active" => "reports.purchases*",
                    ],

                    [
                        "name" => "Laporan Stok",
                        "route" => "reports.stock",
                        "active" => "reports.stock*",
                    ],
                ],
            ],
        ],

        "SYSTEM" => [
            [
                "name" => "Settings",
                "route" => "settings",
                "active" => "settings",
                "icon" => "fas fa-cog",
                "badge" => 0,
                "roles" => ["owner", "admin"],
            ],

            [
                "name" => "User Management",
                "route" => "users.index",
                "active" => "users.*",
                "icon" => "fas fa-users-cog",
                "badge" => 0,
                "roles" => ["owner"],
            ],
        ],
    ];
@endphp

<aside
    class="flex h-full w-64 shrink-0 flex-col bg-ckb-inverse-surface text-ckb-inverse-on-surface"
>
    {{-- Logo --}}
    <div class="flex h-16 items-center gap-4 border-b border-white/10 px-6">
        <img
            src="{{ asset('/assets/photos/warkop-ckb-logo.webp') }}"
            alt="Warkop Cak Kebo"
            loading="lazy"
            class="w-8 rounded-sm"
        />

        <div>
            <h1 class="text-sm font-bold leading-tight">Warkop Cak Kebo</h1>

            <p class="text-[10px] tracking-wider text-ckb-inverse-on-surface/60">ADMIN PANEL</p>
        </div>
    </div>

    {{-- User --}}
    <div class="flex items-center border-b border-white/10 px-6 py-4">
        <img
            src="https://ui-avatars.com/api/?name={{ urlencode($user->username ?? 'User') }}&background=283044&color=fff"
            alt="Profile"
            class="mr-3 h-10 w-10 rounded-full"
        />

        <div class="min-w-0">
            <p class="truncate text-sm font-semibold">
                {{
                    $user->employee->full_name ??
                        ($user->username ?? "Admin")
                }}
            </p>

            <span
                class="mt-1 inline-flex rounded bg-white/10 px-2 py-0.5 text-[10px] uppercase text-ckb-inverse-on-surface/80"
            >
                {{ $userRole }}
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4">
        @foreach ($menus as $category => $items)
            @php
                $visibleItems = array_filter(
                    $items,
                    fn($item) => in_array($userRole, $item["roles"]),
                );
            @endphp

            @if (count($visibleItems) > 0)
                <div class="mb-6">
                    <p
                        class="mb-2 px-6 text-xs font-semibold tracking-wider text-ckb-inverse-on-surface/40"
                    >
                        {{ $category }}
                    </p>

                    <ul class="space-y-0.5">
                        @foreach ($visibleItems as $item)
                            {{-- =========================
                                 NORMAL MENU
                            ========================== --}}
                            @if (($item["type"] ?? "link") === "link")
                                @php
                                    $isActive = request()->routeIs($item["active"]);

                                    $routeUrl = Route::has($item["route"]) ? route($item["route"]) : "#";
                                @endphp

                                <li>
                                    <a
                                        href="{{ $routeUrl }}"
                                        class="
                                            group flex items-center
                                            border-l-4
                                            px-6 py-2.5
                                            text-sm
                                            transition-colors

                                            {{ $isActive
                                                ? 'border-ckb-secondary-container bg-ckb-secondary-container text-ckb-on-secondary-container font-semibold'
                                                : 'border-transparent text-ckb-inverse-on-surface hover:bg-white/5'
                                            }}
                                        "
                                    >
                                        <i
                                            class="
                                                {{ $item['icon'] }}
                                                mr-3 w-5
                                                text-center text-lg

                                                {{ $isActive
                                                    ? 'text-ckb-on-secondary'
                                                    : 'text-ckb-inverse-on-surface/50 group-hover:text-ckb-inverse-on-surface'
                                                }}
                                            "
                                        ></i>

                                        <span class="flex-1">
                                            {{ $item["name"] }}
                                        </span>

                                        @if (($item["badge"] ?? 0) > 0)
                                            <span
                                                class="rounded-full bg-ckb-error px-1.5 py-0.5 text-[10px] font-bold text-ckb-on-error"
                                            >
                                                {{ $item["badge"] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                                {{-- =========================
                                     DROPDOWN MENU
                                ========================== --}}
                            @else
                                @php
                                    $isChildActive = false;

                                    foreach ($item["children"] as $child) {
                                        if (request()->routeIs($child["active"])) {
                                            $isChildActive = true;
                                            break;
                                        }
                                    }
                                @endphp

                                <li>
                                    <details
                                        {{
                                            $isChildActive
                                                ? "open"
                                                : ""
                                        }}
                                        class="group"
                                    >
                                        <summary
                                            class="
                                                flex cursor-pointer
                                                list-none items-center
                                                border-l-4
                                                px-6 py-2.5
                                                text-sm
                                                transition-colors

                                                {{ $isChildActive
                                                    ? 'border-ckb-secondary-container bg-white/5 text-ckb-inverse-on-surface font-semibold'
                                                    : 'border-transparent text-ckb-inverse-on-surface hover:bg-white/5'
                                                }}
                                            "
                                        >
                                            <i
                                                class="
                                                    {{ $item['icon'] }}
                                                    mr-3 w-5
                                                    text-center text-lg

                                                    {{ $isChildActive
                                                        ? 'text-ckb-secondary-container'
                                                        : 'text-ckb-inverse-on-surface/50'
                                                    }}
                                                "
                                            ></i>
                                            <span class="flex-1">
                                                {{ $item["name"] }}
                                            </span>
                                            <i
                                                class="fas fa-chevron-down text-[10px] transition-transform group-open:rotate-180"
                                            ></i>
                                        </summary>

                                        <ul class="mt-1 space-y-0.5">
                                            @foreach ($item["children"] as $child)
                                                @php
                                                    $childActive =
                                                        Route::has($child["active"]) && request()->routeIs($child["active"]);
                                                    $childUrl = Route::has($child["route"]) ? route($child["route"]) : "#";
                                                @endphp
                                                <li>
                                                    <a
                                                        href="{{ $childUrl }}"
                                                        class="
                                                            flex items-center
                                                            border-l-4
                                                            py-2 pl-[3.75rem] pr-6
                                                            text-xs
                                                            transition-colors

                                                            {{ $childActive
                                                                ? 'border-ckb-secondary-container bg-ckb-secondary-container/15 text-ckb-secondary-container font-semibold'
                                                                : 'border-transparent text-ckb-inverse-on-surface/60 hover:bg-white/5 hover:text-ckb-inverse-on-surface'
                                                            }}
                                                        "
                                                    >
                                                        {{ $child["name"] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </nav>
</aside>
