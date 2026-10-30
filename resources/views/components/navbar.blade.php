@props (["title" => null])

@php
    $breadcrumbs = [
        "dashboard" => ["Dashboard"],

        "products.index" => ["Product Management"],

        "products.create" => ["Product Management", "Tambah Produk"],

        "products.edit" => ["Product Management", "Edit Produk"],

        "raw-material.index" => ["Bahan Baku"],

        "supplier.index" => ["Pembelian"],

        "report.transactions" => ["Laporan", "Riwayat Transaksi"],

        "report.sales" => ["Laporan", "Laporan Penjualan"],

        "report.purchases" => ["Laporan", "Laporan Pembelian"],

        "report.stock" => ["Laporan", "Laporan Stok"],

        "settings" => ["Settings"],

        "users.index" => ["User Management"],
    ];

    $currentRoute = request()->route()?->getName();

    $currentBreadcrumbs = $breadcrumbs[$currentRoute] ?? [$title ?? "Admin Panel"];
@endphp

<header
    class="flex h-16 shrink-0 items-center justify-between border-b border-ckb-outline-variant/40 bg-ckb-surface-container-lowest px-6"
>
    {{-- Current Route --}}
    <div class="flex min-w-0 items-center">
        @foreach ($currentBreadcrumbs as $index => $breadcrumb)
            @if ($index > 0)
                <i
                    class="fas fa-chevron-right mx-3 text-[10px] text-ckb-outline"
                ></i>

            @endif

            <h2
                class="
                    truncate
                    {{ $index === count($currentBreadcrumbs) - 1
                        ? 'text-lg font-semibold text-ckb-primary'
                        : 'text-sm font-medium text-ckb-on-surface-variant'
                    }}
                "
            >
                {{ $breadcrumb }}
            </h2>

        @endforeach
    </div>

    {{-- Logout --}}
    <div class="shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button
                type="submit"
                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-ckb-on-surface-variant transition-colors hover:bg-ckb-error-container hover:text-ckb-on-error-container"
            >
                <i class="fas fa-right-from-bracket"></i>

                <span class="hidden sm:inline"> Keluar </span>
            </button>
        </form>
    </div>
</header>
