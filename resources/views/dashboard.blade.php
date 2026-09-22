<x-layout title="Dashboard">
    <!-- Konten Spesifik Halaman Dashboard (Sesuai Gambar Figma) -->

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-ckb-primary">Dashboard</h2>
        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }} — Shift Pagi 06:00-14:00</p>
    </div>

    <!-- Statistik Cards (Contoh) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Omzet Hari Ini</p>
            <h3 class="text-xl font-bold text-ckb-primary">Rp 847.500</h3>
            <p class="text-[10px] text-green-600 font-medium mt-1">+12.4% vs kemarin</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
            <h3 class="text-xl font-bold text-ckb-primary">94</h3>
            <p class="text-[10px] text-green-600 font-medium mt-1">+8 dari kemarin</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Produk Aktif</p>
            <h3 class="text-xl font-bold text-ckb-primary">10</h3>
            <p class="text-[10px] text-gray-400 mt-1">dari 11 total</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Bahan Stok Kritis</p>
            <h3 class="text-xl font-bold text-ckb-primary">4</h3>
            <p class="text-[10px] text-red-500 font-medium mt-1">perlu restok segera</p>
        </div>
    </div>

    <!-- Tempatkan Chart atau Tabel di sini nantinya -->
    <div class="bg-white h-64 rounded-xl border border-gray-100 shadow-sm flex items-center justify-center text-gray-400">
        Area Grafik & Data Laporan
    </div>

</x-layout>
