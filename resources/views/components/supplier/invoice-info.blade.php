<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="font-bold text-ckb-primary mb-4 border-b border-gray-100 pb-2">
        Informasi Nota Pembelian
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">
                Nomor Invoice *
            </label>

            <input
                type="text"
                id="invoice_number"
                maxlength="20"
                placeholder="cth: INV-0106-0047"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none"
                required
            />
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">
                Nama Supplier *
            </label>

            <input
                type="text"
                id="supplier_name"
                maxlength="50"
                list="supplier-suggestions"
                placeholder="Pilih / Ketik Nama Supplier"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none"
                required
            />

            <datalist id="supplier-suggestions"></datalist>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">
                Tanggal Beli *
            </label>

            <input
                type="date"
                id="purchase_date"
                required
                max="{{ date('Y-m-d') }}"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none"
            />
        </div>
    </div>
</div>
