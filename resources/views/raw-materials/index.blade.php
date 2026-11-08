<x-layout title="Manajemen Bahan Baku">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-ckb-on-surface-variant">Inventori Operasional</p>
            <h2 class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl">
                Master Bahan Baku
            </h2>
            <p id="summary-text" class="mt-1 text-xs text-gray-500">Memuat ringkasan stok...</p>
        </div>

        <button
            type="button"
            id="btn-add-material"
            class="inline-flex shrink-0 items-center justify-center rounded-lg bg-ckb-primary px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-ckb-accent sm:text-sm"
        >
            + Tambah Bahan Baku
        </button>
    </div>

    {{-- Critical stock alert --}}
    <div
        id="critical-alert"
        class="hidden flex items-center justify-between gap-3 rounded-xl border border-red-200 bg-red-50 p-3 text-ckb-error mb-4"
    >
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <div>
                <p id="alert-title" class="font-bold text-sm">Bahan baku dalam kondisi kritis!</p>
                <p class="text-xs">Segera lakukan restok melalui Pembelian Supplier untuk menghindari gangguan operasional.</p>
            </div>
        </div>

        <a
            href="{{ route('supplier.index') }}"
            class="text-sm font-semibold underline hover:text-red-800"
        >
            Buat PO Sekarang
        </a>
    </div>

    {{-- Material table --}}
    <x-raw-material.packaging-row />

    {{-- Material modal --}}
    <x-raw-material.material-modal />

    {{-- Edit Material modal --}}
    <x-raw-material.packaging-modal />

    <x-slot:script>
        @vite ("resources/js/raw-material/index.js")
    </x-slot:script>
</x-layout>
