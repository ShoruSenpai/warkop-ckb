@php
    use App\Support\AdminNavigation;

    $user = auth()->user();

    $userRole = strtolower($user?->role ?? "employee");

    $displayName = $user?->employee?->full_name ?: $user?->username ?? "Admin";

    $roleLabels = [
        "owner" => "Owner",
        "admin" => "Administrator",
    ];

    $roleLabel = $roleLabels[$userRole] ?? ucfirst($userRole);

    $groups = AdminNavigation::visibleGroups($userRole);
@endphp

<aside
    class="sticky top-0 flex h-dvh w-56 shrink-0 flex-col overflow-hidden bg-ckb-inverse-surface text-ckb-inverse-on-surface xl:w-60 2xl:w-64"
>
    {{-- BRAND --}}
    <div class="flex h-16 shrink-0 items-center gap-2.5 px-4">
        <div
            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-ckb-secondary"
        >
            <img
                src="{{ asset('assets/photos/warkop-ckb-logo.webp') }}"
                alt="Warkop Cak Kebo"
                class="max-h-full max-w-full object-contain rounded-lg"
            />
        </div>

        <div class="min-w-0">
            <h1
                class="truncate text-sm font-bold leading-tight text-ckb-inverse-on-surface"
            >
                Warkop Cak Kebo
            </h1>

            <p
                class="mt-0.5 text-[9px] font-semibold uppercase tracking-[0.1em] text-ckb-inverse-on-surface/70"
            >Admin Workspace</p>
        </div>
    </div>

    {{-- USER PROFILE --}}
    <div class="mx-3 shrink-0">
        <div
            class="flex items-center gap-2.5 rounded-xl border border-ckb-inverse-on-surface/15 bg-ckb-inverse-on-surface/5 px-2.5 py-2"
        >
            {{-- Avatar --}}
            <div class="relative shrink-0">
                <img
                    data-ckb-avatar="{{ $displayName }}"
                    alt="{{ $displayName }}"
                    class="size-9 rounded-full object-cover"
                />

                <span
                    class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-ckb-inverse-surface bg-ckb-success"
                ></span>
            </div>

            {{-- User Information --}}
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-ckb-inverse-on-surface">
                    {{ $displayName }}
                </p>

                <p
                    class="mt-1 truncate text-[11px] text-ckb-inverse-on-surface/70"
                >
                    {{ $roleLabel }}
                </p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="min-h-0 flex-1 overflow-y-auto px-3 pb-3 pt-4">
        @foreach ($groups as $group)
            <div class="mb-4">
                {{-- GROUP TITLE --}}
                <p
                    class="mb-1.5 px-2 text-[10px] font-bold tracking-[0.1em] text-ckb-inverse-on-surface/70"
                >
                    {{
                        $group[
                            "label"
                        ]
                    }}
                </p>

                <ul class="space-y-1">
                    @foreach ($group["items"] as $item)
                        @php
                            $isDropdown = !empty($item["children"]);

                            $isActive = !$isDropdown && AdminNavigation::isActive($item);

                            $itemUrl = AdminNavigation::url($item);
                        @endphp

                        {{-- NORMAL MENU --}}
                        @if (!$isDropdown)
                            <li>
                                <a
                                    href="{{ $itemUrl ?? '#' }}"
                                    @if ($isActive)
                                        aria-current="page"
                                    @endif
                                    class="
                                        group flex min-h-9 items-center
                                        gap-2.5 rounded-lg px-3 py-2
                                        text-[13px] transition-all duration-200

                                        {{ $isActive
                                            ? 'bg-ckb-secondary-container font-semibold text-ckb-on-secondary-container shadow-surface'
                                            : 'text-ckb-inverse-on-surface/80 hover:bg-ckb-inverse-on-surface/5 hover:text-ckb-inverse-on-surface'
                                        }}
                                    "
                                >
                                    {{-- ICON --}}
                                    <i
                                        class="
                                            {{ $item['icon'] }}
                                            w-4 shrink-0 text-center text-sm

                                            {{ $isActive
                                                ? 'text-ckb-on-secondary-container'
                                                : 'text-ckb-inverse-on-surface/70 group-hover:text-ckb-inverse-on-surface'
                                            }}
                                        "
                                    ></i>

                                    {{-- LABEL --}}
                                    <span class="min-w-0 flex-1 truncate">
                                        {{ $item["label"] }}
                                    </span>

                                    {{-- BADGE --}}
                                    @if (($item["badge"] ?? 0) > 0)
                                        <span
                                            class="rounded-full bg-ckb-error px-2 py-0.5 text-[10px] font-bold text-ckb-on-error"
                                        >
                                            {{ $item["badge"] }}
                                        </span>
                                    @endif

                                    {{-- ACTIVE INDICATOR --}}
                                    {{--                                    @if ($isActive)--}}
                                    {{--                                        <span--}}
                                    {{--                                            class="size-2 shrink-0 rounded-full bg-ckb-on-secondary-container"--}}
                                    {{--                                        ></span>--}}
                                    {{--                                    @endif--}}
                                </a>
                            </li>

                            {{-- DROPDOWN MENU --}}
                        @else
                            @php
                                $isChildActive = collect($item["children"])->contains(
                                    fn($child) => AdminNavigation::isActive($child),
                                );
                            @endphp

                            <li>
                                <details
                                    class="group"
                                    @if ($isChildActive) open @endif
                                >
                                    <summary
                                        class="
                                            flex min-h-9 cursor-pointer
                                            list-none items-center gap-2.5
                                            rounded-lg px-3 py-2
                                            text-[13px] transition-colors

                                            {{ $isChildActive
                                                ? 'bg-ckb-inverse-on-surface/5 font-semibold text-ckb-inverse-on-surface'
                                                : 'text-ckb-inverse-on-surface/80 hover:bg-ckb-inverse-on-surface/5 hover:text-ckb-inverse-on-surface'
                                            }}
                                        "
                                    >
                                        <i
                                            class="{{ $item['icon'] }}
                                                   w-4 shrink-0 text-center
                                                   text-sm text-ckb-inverse-on-surface/70"
                                        ></i>

                                        <span class="min-w-0 flex-1 truncate">
                                            {{ $item["label"] }}
                                        </span>

                                        <i
                                            class="fas fa-chevron-down shrink-0 text-[10px] transition-transform duration-200 group-open:rotate-180"
                                        ></i>
                                    </summary>

                                    {{-- CHILDREN --}}
                                    <ul class="mt-1 space-y-0.5">
                                        @foreach ($item["children"] as $child)
                                            @php
                                                $childActive = AdminNavigation::isActive($child);

                                                $childUrl = AdminNavigation::url($child);
                                            @endphp

                                            <li>
                                                <a
                                                    href="{{ $childUrl ?? '#' }}"
                                                    @if ($childActive)
                                                        aria-current="page"
                                                    @endif
                                                    class="
                                                        flex min-h-8 items-center
                                                        rounded-lg py-1.5 pl-9 pr-2.5
                                                        text-xs transition-colors

                                                        {{ $childActive
                                                            ? 'bg-ckb-secondary-container font-semibold text-ckb-on-secondary-container'
                                                            : 'text-ckb-inverse-on-surface/70 hover:bg-ckb-inverse-on-surface/5 hover:text-ckb-inverse-on-surface'
                                                        }}
                                                    "
                                                >
                                                    {{
                                                        $child[
                                                            "label"
                                                        ]
                                                    }}
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

        @endforeach
    </nav>

    {{-- LOGOUT --}}
    <div class="mt-auto shrink-0 px-3 pb-3 pt-2">
        <div class="border-t border-ckb-inverse-on-surface/10 pt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="flex min-h-10 w-full items-center gap-2.5 rounded-lg border border-ckb-inverse-on-surface/15 bg-ckb-inverse-on-surface/5 px-3 py-2 text-[13px] text-ckb-inverse-on-surface/80 transition-colors hover:bg-ckb-inverse-on-surface/10 hover:text-ckb-inverse-on-surface"
                >
                    <i class="fas fa-right-from-bracket w-5 text-center"></i>

                    <span>Keluar / Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
