<x-layout title="Master Bahan Baku">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-ckb-secondary">Master Bahan Baku</h2>
            <p id="summary-text" class="text-sm text-gray-500">Memuat ringkasan stok...</p>
        </div>

        <button type="button" id="btn-add-material"
                class="bg-ckb-secondary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-ckb-accent transition">
            + Tambah Bahan Baku
        </button>
    </div>

    {{-- Critical stock alert --}}
    <div id="critical-alert"
         class="hidden bg-red-50 border border-red-200 text-ckb-error p-4 rounded-xl mb-6 flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <div>
                <p id="alert-title" class="font-bold text-sm">
                    Bahan baku dalam kondisi kritis!
                </p>
                <p class="text-xs">
                    Segera lakukan restok melalui Pembelian Supplier untuk menghindari gangguan operasional.
                </p>
            </div>
        </div>

        <a href="{{ route('supplier.index') }}"
           class="text-sm font-semibold underline hover:text-red-800">
            Buat PO Sekarang
        </a>
    </div>

    {{-- Material table --}}
    <x-raw-material.packaging-row/>

    {{-- Material modal --}}
    <x-raw-material.material-modal/>

    {{-- Packaging modal --}}
    <x-raw-material.packaging-modal/>

    <x-slot:script>
        @vite('resources/js/raw-material/index.js')
    </x-slot:script>

</x-layout>
