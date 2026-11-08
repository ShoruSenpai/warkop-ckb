<x-layout title="Dashboard">
    <!-- Konten Spesifik Halaman Dashboard (Sesuai Gambar Figma) -->

    <div class="mb-4">
        <h2 class="text-xl font-bold text-ckb-primary">Dashboard</h2>
        <p class="text-xs text-gray-500">{{
            \Carbon\Carbon::now()->translatedFormat(
                "l, d M Y",
            )
        }} — Shift Pagi 06:00-14:00</p>
    </div>

    <!-- Statistik Cards (Contoh) -->
    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
            <p class="mb-1 text-[11px] text-gray-500">Omzet Hari Ini</p>
            <h3 class="text-lg font-bold text-ckb-primary">Rp 847.500</h3>
            <p class="mt-1 text-[10px] font-medium text-green-600">+12.4% vs kemarin</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
            <p class="mb-1 text-[11px] text-gray-500">Total Transaksi</p>
            <h3 class="text-lg font-bold text-ckb-primary">94</h3>
            <p class="mt-1 text-[10px] font-medium text-green-600">+8 dari kemarin</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
            <p class="mb-1 text-[11px] text-gray-500">Produk Aktif</p>
            <h3 class="text-lg font-bold text-ckb-primary">10</h3>
            <p class="mt-1 text-[10px] text-gray-400">dari 11 total</p>
        </div>
        <div class="rounded-xl border border-red-100 bg-white p-3 shadow-sm">
            <p class="mb-1 text-[11px] text-gray-500">Bahan Stok Kritis</p>
            <h3 class="text-lg font-bold text-ckb-primary">4</h3>
            <p class="mt-1 text-[10px] font-medium text-red-500">perlu restok segera</p>
        </div>
    </div>

    <!-- Tempatkan Chart atau Tabel di sini nantinya -->
    <div
        class="flex h-56 items-center justify-center rounded-xl border border-gray-100 bg-white text-sm text-gray-400 shadow-sm"
    >
        Area Grafik & Data Laporan
    </div>
</x-layout>
