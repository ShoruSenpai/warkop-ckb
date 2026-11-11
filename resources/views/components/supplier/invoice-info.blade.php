<div class="bg-ckb-surface rounded-xl shadow-surface border border-ckb-outline-variant/50 p-4 mb-4">
    <h3 class="font-bold text-sm text-ckb-primary mb-3 border-b border-ckb-outline-variant/50 pb-2">
        Informasi Nota Pembelian
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-semibold text-ckb-on-surface-variant mb-1">
                Nomor Invoice *
            </label>

            <input
                type="text"
                id="invoice_number"
                maxlength="20"
                placeholder="cth: INV-0106-0047"
                class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                required
            />
        </div>

        <div>
            <label class="block text-xs font-semibold text-ckb-on-surface-variant mb-1">
                Nama Supplier *
            </label>

            <input
                type="text"
                id="supplier_name"
                maxlength="50"
                list="supplier-suggestions"
                placeholder="Pilih / Ketik Nama Supplier"
                class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                required
            />

            <datalist id="supplier-suggestions"></datalist>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ckb-on-surface-variant mb-1">
                Tanggal Beli *
            </label>

            <input
                type="date"
                id="purchase_date"
                required
                max="{{ date('Y-m-d') }}"
                class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
            />
        </div>
    </div>
</div>
