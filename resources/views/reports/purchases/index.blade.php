<x-layout title="Laporan Pembelian">
    <div class="space-y-4">
        {{-- Header --}}
        <div>
            <h2 class="text-xl font-bold text-ckb-primary">
                Laporan Pembelian
            </h2>

            <p
                id="summary-text"
                class="mt-1 text-sm text-ckb-on-surface-variant"
            >Memuat data pembelian...</p>
        </div>

        {{-- Table --}}
        <div
            class="overflow-hidden rounded-xl border border-ckb-outline-variant/40 bg-ckb-surface-container-lowest"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <thead
                        class="border-b border-ckb-outline-variant/40 bg-ckb-surface-container-low"
                    >
                        <tr
                            class="text-left text-xs uppercase tracking-wider text-ckb-on-surface-variant"
                        >
                            <th class="px-3 py-2.5 text-xs font-semibold">
                                Nama Supplier
                            </th>

                            <th class="px-3 py-2.5 text-xs font-semibold">
                                Tanggal Pembelian
                            </th>

                            <th class="px-3 py-2.5 text-xs font-semibold">
                                Invoice Number
                            </th>

                            <th class="px-3 py-2.5 text-xs font-semibold">Status</th>
                        </tr>
                    </thead>

                    <tbody id="purchase-report-list">
                        <tr>
                            <td
                                colspan="4"
                                class="px-4 py-10 text-center text-sm text-ckb-outline"
                            >
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Memuat data pembelian...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @vite ("resources/js/reports/purchase/index.js")
</x-layout>
