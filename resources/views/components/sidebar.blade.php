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

    $avatarUrl =
        "https://ui-avatars.com/api/?name=" .
        urlencode($displayName) .
        "&background=DBE6F5&color=14294A&bold=true";
@endphp

<aside
    class="sticky top-0 flex h-screen w-[290px] shrink-0 flex-col overflow-hidden bg-ckb-inverse-surface text-ckb-inverse-on-surface"
>
    {{-- BRAND --}}
    <div class="flex h-16 shrink-0 items-center gap-3 px-5">
        <div
            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-ckb-secondary-container p-1.5"
        >
            <img
                src="{{ asset('assets/photos/warkop-ckb-logo.webp') }}"
                alt="Warkop Cak Kebo"
                class="max-h-full max-w-full object-contain"
            />
        </div>

        <div class="min-w-0">
            <h1 class="truncate text-sm font-bold leading-tight text-white">
                Warkop Cak Kebo
            </h1>

            <p
                class="mt-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-ckb-inverse-on-surface/50"
            >Admin Workspace</p>
        </div>
    </div>

    {{-- USER PROFILE --}}
    <div class="mx-4 mt-5 shrink-0">
        <div
            class="flex items-center gap-3 rounded-xl border border-white/15 bg-white/5 px-3 py-3"
        >
            {{-- Avatar --}}
            <div class="relative shrink-0">
                <img
                    src="{{ $avatarUrl }}"
                    alt="{{ $displayName }}"
                    class="size-10 rounded-full object-cover"
                />

                <span
                    class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-ckb-inverse-surface bg-blue-400"
                ></span>
            </div>

            {{-- User Information --}}
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">
                    {{ $displayName }}
                </p>

                <p
                    class="mt-1 truncate text-[11px] text-ckb-inverse-on-surface/70"
                >
                    {{ $roleLabel }}
                </p>
            </div>

            <i class="fas fa-chevron-right shrink-0 text-xs text-white/50"></i>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="min-h-0 flex-1 overflow-y-auto px-4 pb-4 pt-6">
        @foreach ($groups as $group)
            <div class="mb-6">
                {{-- GROUP TITLE --}}
                <p
                    class="mb-2 px-2 text-[11px] font-bold tracking-[0.12em] text-ckb-inverse-on-surface/45"
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
                                        group flex min-h-11 items-center
                                        gap-3 rounded-xl px-3.5 py-2.5
                                        text-sm transition-all duration-200

                                        {{ $isActive
                                            ? 'bg-ckb-secondary-container font-semibold text-ckb-on-secondary-container shadow-sm'
                                            : 'text-ckb-inverse-on-surface/80 hover:bg-white/5 hover:text-white'
                                        }}
                                    "
                                >
                                    {{-- ICON --}}
                                    <i
                                        class="
                                            {{ $item['icon'] }}
                                            w-5 shrink-0 text-center text-base

                                            {{ $isActive
                                                ? 'text-ckb-on-secondary-container'
                                                : 'text-ckb-inverse-on-surface/55 group-hover:text-white'
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
                                            flex min-h-11 cursor-pointer
                                            list-none items-center gap-3
                                            rounded-xl px-3.5 py-2.5
                                            text-sm transition-colors

                                            {{ $isChildActive
                                                ? 'bg-white/5 font-semibold text-white'
                                                : 'text-ckb-inverse-on-surface/80 hover:bg-white/5 hover:text-white'
                                            }}
                                        "
                                    >
                                        <i
                                            class="{{ $item['icon'] }}
                                                   w-5 shrink-0 text-center
                                                   text-base text-ckb-inverse-on-surface/55"
                                        ></i>

                                        <span class="min-w-0 flex-1 truncate">
                                            {{ $item["label"] }}
                                        </span>

                                        <i
                                            class="fas fa-chevron-down shrink-0 text-[10px] transition-transform duration-200 group-open:rotate-180"
                                        ></i>
                                    </summary>

                                    {{-- CHILDREN --}}
                                    <ul class="mt-1 space-y-1">
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
                                                        flex min-h-10 items-center
                                                        rounded-lg py-2 pl-11 pr-3
                                                        text-[13px] transition-colors

                                                        {{ $childActive
                                                            ? 'bg-ckb-secondary-container font-semibold text-ckb-on-secondary-container'
                                                            : 'text-ckb-inverse-on-surface/70 hover:bg-white/5 hover:text-white'
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
    <div class="mt-auto shrink-0 px-4 pb-5 pt-3">
        <div class="border-t border-white/10 pt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="flex min-h-12 w-full items-center gap-3 rounded-xl border border-white/15 bg-white/5 px-3.5 py-3 text-sm text-ckb-inverse-on-surface/80 transition-colors hover:bg-white/10 hover:text-white"
                >
                    <i class="fas fa-right-from-bracket w-5 text-center"></i>

                    <span>Keluar / Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
