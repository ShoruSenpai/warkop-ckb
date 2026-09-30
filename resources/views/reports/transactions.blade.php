<x-layout title="Riwayat Transaksi">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-ckb-primary">Riwayat Transaksi</h2>
                <p class="mt-1 text-sm text-ckb-on-surface-variant">Daftar seluruh transaksi kasir dan rincian struk belanja.</p>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Mulai</label>
                    <input type="date" id="filter-start-date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Akhir</label>
                    <input type="date" id="filter-end-date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Metode Pembayaran</label>
                    <select id="filter-payment-method" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-ckb-primary">
                        <option value="">Semua Metode</option>
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Cari No. Faktur</label>
                    <input type="text" id="filter-search" placeholder="Contoh: INV-..." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="btn-reset-filter" class="px-4 py-2 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                    Reset Filter
                </button>
                <button type="button" id="btn-apply-filter" class="px-4 py-2 rounded-lg bg-ckb-primary text-xs font-semibold text-white hover:bg-ckb-accent">
                    Terapkan Filter
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Faktur</th>
                            <th class="px-4 py-3 font-semibold">Waktu Transaksi</th>
                            <th class="px-4 py-3 font-semibold">Kasir</th>
                            <th class="px-4 py-3 font-semibold">Metode Bayar</th>
                            <th class="px-4 py-3 font-semibold">Gross</th>
                            <th class="px-4 py-3 font-semibold">Diskon</th>
                            <th class="px-4 py-3 font-semibold">Total Bayar</th>
                            <th class="px-4 py-3 text-right font-semibold">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-list">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat riwayat transaksi...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="pagination-container" class="flex justify-between items-center px-4 py-3 border-t border-gray-100 text-xs text-gray-500">
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div id="tx-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-ckb-primary text-white">
                <div>
                    <h3 class="font-bold text-base">Rincian Transaksi</h3>
                    <p id="modal-invoice-code" class="text-xs opacity-80">-</p>
                </div>
                <button type="button" id="btn-close-modal" class="text-white/80 hover:text-white">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-2 text-xs border-b border-gray-100 pb-3">
                    <div>
                        <span class="text-gray-400">Waktu:</span>
                        <p id="modal-tx-time" class="font-semibold text-gray-700">-</p>
                    </div>
                    <div>
                        <span class="text-gray-400">Kasir:</span>
                        <p id="modal-cashier-name" class="font-semibold text-gray-700">-</p>
                    </div>
                    <div>
                        <span class="text-gray-400">Metode Bayar:</span>
                        <p id="modal-payment-method" class="font-semibold text-gray-700 uppercase">-</p>
                    </div>
                    <div>
                        <span class="text-gray-400">Status:</span>
                        <p id="modal-payment-status" class="font-semibold text-green-600 uppercase">-</p>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Item Pembelian</h4>
                    <ul id="modal-items-list" class="divide-y divide-gray-100">
                    </ul>
                </div>

                <div class="border-t border-gray-100 pt-3 space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotal Gross:</span>
                        <span id="modal-gross-amount" class="font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-red-500">
                        <span>Potongan Diskon:</span>
                        <span id="modal-discount-amount" class="font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-ckb-primary pt-2 border-t border-gray-100">
                        <span>Total Akhir:</span>
                        <span id="modal-final-amount">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        @vite("resources/js/reports/transactions.js")
    </x-slot:script>
</x-layout>
