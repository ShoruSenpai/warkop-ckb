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
    class="flex h-14 shrink-0 items-center justify-between border-b border-ckb-outline-variant/40 bg-ckb-surface-container-lowest px-4 sm:px-5"
>
    {{-- Current Route --}}
    <div class="flex min-w-0 items-center">
        @foreach ($currentBreadcrumbs as $index => $breadcrumb)
            @if ($index > 0)
                <i
                    class="fas fa-chevron-right mx-2 text-[9px] text-ckb-outline"
                ></i>

            @endif

            <h2
                class="
                    truncate
                    {{ $index === count($currentBreadcrumbs) - 1
                        ? 'text-base font-semibold text-ckb-primary'
                        : 'text-xs font-medium text-ckb-on-surface-variant'
                    }}
                "
            >
                {{ $breadcrumb }}
            </h2>

        @endforeach
    </div>

    <div class="flex shrink-0 items-center gap-2">
        <button
            type="button"
            aria-label="Notifikasi"
            class="relative flex size-9 items-center justify-center rounded-xl bg-ckb-surface-container-low text-sm text-ckb-primary transition hover:bg-ckb-surface-container"
        >
            <i class="fas fa-bell"></i>
            <span class="absolute right-2 top-2 size-1.5 rounded-full bg-ckb-error"></span>
        </button>

        <div class="inline-flex h-9 items-center gap-2 rounded-xl bg-ckb-secondary-container px-3 text-xs font-semibold text-ckb-on-secondary-container">
            <span class="size-1.5 rounded-full bg-ckb-primary"></span>
            <span class="hidden sm:inline">Toko Buka</span>
            <span class="sm:hidden">Buka</span>
        </div>
    </div>
</header>
