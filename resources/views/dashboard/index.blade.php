<x-layout title="Dashboard">
    <!-- Konten Spesifik Halaman Dashboard (Sesuai Gambar Figma) -->

    <div class="mb-4">
        <h2 class="text-xl font-bold text-ckb-primary">Dashboard</h2>
        <p class="text-xs text-ckb-on-surface-variant">{{
            \Carbon\Carbon::now()->translatedFormat(
                "l, d M Y",
            )
        }} — Shift Pagi 06:00-14:00</p>
    </div>

    <!-- Statistik Cards (Contoh) -->
    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3 shadow-surface">
            <p class="mb-1 text-[11px] text-ckb-on-surface-variant">Omzet Hari Ini</p>
            <h3 class="text-lg font-bold text-ckb-primary">Rp 847.500</h3>
            <p class="mt-1 text-[10px] font-medium text-ckb-success">+12.4% vs kemarin</p>
        </div>
        <div class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3 shadow-surface">
            <p class="mb-1 text-[11px] text-ckb-on-surface-variant">Total Transaksi</p>
            <h3 class="text-lg font-bold text-ckb-primary">94</h3>
            <p class="mt-1 text-[10px] font-medium text-ckb-success">+8 dari kemarin</p>
        </div>
        <div class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3 shadow-surface">
            <p class="mb-1 text-[11px] text-ckb-on-surface-variant">Produk Aktif</p>
            <h3 class="text-lg font-bold text-ckb-primary">10</h3>
            <p class="mt-1 text-[10px] text-ckb-on-surface-variant">dari 11 total</p>
        </div>
        <div class="rounded-xl border border-ckb-error/30 bg-ckb-surface p-3 shadow-surface">
            <p class="mb-1 text-[11px] text-ckb-on-surface-variant">Bahan Stok Kritis</p>
            <h3 class="text-lg font-bold text-ckb-primary">4</h3>
            <p class="mt-1 text-[10px] font-medium text-ckb-error">perlu restok segera</p>
        </div>
    </div>

    <!-- Tempatkan Chart atau Tabel di sini nantinya -->
    <div
        class="flex h-56 items-center justify-center rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface text-sm text-ckb-on-surface-variant shadow-surface"
    >
        Area Grafik & Data Laporan
    </div>
</x-layout>
