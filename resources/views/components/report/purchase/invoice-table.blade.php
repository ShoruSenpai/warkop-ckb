<section
    class="overflow-hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface shadow-surface"
    aria-label="Daftar invoice pembelian"
>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] border-collapse text-left">
            <thead
                class="border-b border-ckb-outline-variant/50 bg-ckb-surface-container-low"
            >
                <tr
                    class="border-b border-ckb-outline-variant/50 text-[10px] font-medium uppercase tracking-wider text-ckb-on-surface-variant"
                >
                    <th scope="col" class="px-3 py-2.5 font-medium">
                        Nomor invoice
                    </th>
                    <th scope="col" class="px-3 py-2.5 font-medium">
                        Nama supplier
                    </th>
                    <th scope="col" class="px-3 py-2.5 font-medium">Tanggal</th>
                    <th scope="col" class="px-3 py-2.5 font-medium">
                        Item dibeli
                    </th>
                    <th scope="col" class="px-3 py-2.5 text-right font-medium">
                        Total pembelian
                    </th>
                    <th scope="col" class="px-3 py-2.5 text-right font-medium">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody data-invoice-list aria-live="polite">
                <tr>
                    <td
                        colspan="6"
                        class="px-3 py-6 text-center text-xs text-ckb-on-surface-variant"
                    >
                        Memuat invoice pembelian...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div
        data-invoice-pagination
        class="flex flex-wrap items-center justify-between gap-3 border-t border-ckb-outline-variant/50 px-3 py-2.5"
        hidden
    >
        <p data-pagination-status class="text-xs text-ckb-on-surface-variant"></p>
        <div class="flex gap-2">
            <button
                type="button"
                data-previous-page
                class="min-h-11 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container-low disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
            >
                Sebelumnya
            </button>
            <button
                type="button"
                data-next-page
                class="min-h-11 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container-low disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
            >
                Berikutnya
            </button>
        </div>
    </div>
</section>
