<div
    class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface"
>
    <h3
        class="mb-3 border-b border-ckb-outline-variant/50 pb-2 text-sm font-bold text-ckb-primary"
    >
        Informasi Nota Pembelian
    </h3>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div>
            <label
                for="invoice_number"
                class="mb-1 block text-xs font-semibold text-ckb-primary"
            >
                Nomor Invoice *
            </label>

            <input
                type="text"
                id="invoice_number"
                maxlength="20"
                placeholder="cth: INV-0106-0047"
                class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                required
            />
        </div>

        <div>
            <label
                for="supplier_name"
                class="mb-1 block text-xs font-semibold text-ckb-primary"
            >
                Nama Supplier *
            </label>

            <input
                type="text"
                id="supplier_name"
                maxlength="50"
                list="supplier-suggestions"
                placeholder="Pilih / Ketik Nama Supplier"
                class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                required
            />

            <datalist id="supplier-suggestions"></datalist>
        </div>

        <div>
            <label
                for="purchase_date"
                class="mb-1 block text-xs font-semibold text-ckb-primary"
            >
                Tanggal Beli *
            </label>

            <input
                type="date"
                id="purchase_date"
                required
                max="{{ date('Y-m-d') }}"
                class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
            />
        </div>
    </div>
</div>
