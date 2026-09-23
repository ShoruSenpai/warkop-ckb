<x-layout title="Pembelian Supplier">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-ckb-primary">Input Pembelian Supplier</h2>
            <p class="text-sm text-gray-500">Catat nota belanja dan update stok secara otomatis</p>
        </div>
    </div>

    <form id="form-purchase" onsubmit="submitPurchase(event)">

        <!-- Component 1: Header Nota -->
        <x-supplier.invoice-info />

        <!-- Daftar Item Belanja -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                <h3 class="font-bold text-ckb-primary">Daftar Item Belanja</h3>
                <button type="button" onclick="addRow()" class="text-sm font-semibold text-ckb-primary hover:text-ckb-accent bg-gray-50 px-3 py-1.5 rounded border border-gray-200">+ Tambah Baris</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left" id="purchase-table">
                    <thead>
                    <tr class="text-[10px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-2 w-64">Nama Barang (Bahan / Eceran)*</th>
                        <th class="pb-2 w-32">Satuan Beli*</th>
                        <th class="pb-2 w-20">Jumlah*</th>
                        <th class="pb-2 w-36">Isi/Konversi*</th>
                        <th class="pb-2 w-36">Harga Satuan*</th>
                        <th class="pb-2 w-36">Subtotal</th>
                        <th class="pb-2 w-10 text-center">Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="item-list">
                    <!-- Baris akan dirender dinamis via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Component 3: Footer Summary & Action -->
            <x-supplier.summary-footer />
        </div>
    </form>

    <template id="row-template">
        <x-supplier.item-row />
    </template>

    <script>
        let inventoryItems = [];

        document.getElementById('purchase_date').valueAsDate = new Date();

        document.addEventListener('DOMContentLoaded', async () => {
            await Promise.all([
                fetchInventoryItems(),
                fetchSupplierSuggestions()
            ]);

            addRow();
        });

        async function fetchInventoryItems() {
            try {
                const response = await fetch('/api/inventory-items', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (response.ok) {
                    inventoryItems = result.data;
                } else {
                    console.error(
                        'Inventory API Error:',
                        response.status,
                        result
                    );
                }

            } catch (err) {
                console.error('Failed to load inventory items:', err);
            }
        }

        async function fetchSupplierSuggestions() {
            try {
                const response = await fetch('/api/supplier-suggestions', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (response.ok) {
                    const datalist =
                        document.getElementById('supplier-suggestions');

                    datalist.innerHTML = result.data
                        .map(s => `<option value="${s}">`)
                        .join('');
                } else {
                    console.error(
                        'Supplier API Error:',
                        response.status,
                        result
                    );
                }

            } catch (err) {
                console.error('Failed to load supplier suggestions:', err);
            }
        }

        function populateSelectOptions(selectEl) {
            selectEl.innerHTML =
                '<option value="" disabled selected>-- Pilih Barang --</option>' +
                inventoryItems.map(item => `
                <option
                    value="${item.item_key}"
                    data-raw-id="${item.raw_material_id || ''}"
                    data-prod-id="${item.product_id || ''}"
                    data-unit="${item.base_unit}">
                    ${item.name}
                </option>
            `).join('');
        }

        function addRow() {
            const template = document.getElementById('row-template');
            const clone = template.content.cloneNode(true);
            const selectEl = clone.querySelector('.item-select');

            populateSelectOptions(selectEl);

            document
                .getElementById('item-list')
                .appendChild(clone);

            updateSummary();
        }

        function removeRow(btn) {
            const rows = document.querySelectorAll('.item-row');

            if (rows.length > 1) {
                btn.closest('tr').remove();
                updateSummary();
            } else {
                alert('Minimal harus ada 1 item belanja.');
            }
        }

        function onItemChange(selectEl) {
            const selectedOption =
                selectEl.options[selectEl.selectedIndex];

            const row = selectEl.closest('tr');

            const baseUnit =
                selectedOption.dataset.unit || '-';

            row.querySelector('.base-unit-text').innerText =
                baseUnit;
        }

        function calculateRow(element) {
            const row = element.closest('tr');

            const qty =
                parseFloat(row.querySelector('.qty').value) || 0;

            const price =
                parseFloat(row.querySelector('.price').value) || 0;

            const subtotal = qty * price;

            row.querySelector('.subtotal').value =
                'Rp ' + subtotal.toLocaleString('id-ID');

            row.dataset.subtotalValue = subtotal;

            updateSummary();
        }

        function updateSummary() {
            let grandTotal = 0;

            const rows =
                document.querySelectorAll('.item-row');

            rows.forEach(row => {
                grandTotal +=
                    parseFloat(row.dataset.subtotalValue || 0);
            });

            document.getElementById('grand-total').innerText =
                'Rp ' + grandTotal.toLocaleString('id-ID');

            document.getElementById('item-count').innerText =
                `${rows.length} item valid · stok akan diperbarui otomatis`;
        }

        async function submitPurchase(e) {
            e.preventDefault();

            const btn =
                document.getElementById('btn-submit');

            btn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

            btn.disabled = true;

            const items = [];
            let isValid = true;

            document.querySelectorAll('.item-row').forEach((row, idx) => {
                const selectEl =
                    row.querySelector('.item-select');

                const selectedOption =
                    selectEl.options[selectEl.selectedIndex];

                if (!selectEl.value) {
                    alert(
                        `Pilih barang terlebih dahulu pada baris ke-${idx + 1}`
                    );

                    isValid = false;
                    return;
                }

                items.push({
                    raw_material_id:
                        selectedOption.dataset.rawId || null,

                    product_id:
                        selectedOption.dataset.prodId || null,

                    purchase_unit:
                    row.querySelector('.unit').value,

                    quantity:
                        parseFloat(
                            row.querySelector('.qty').value
                        ),

                    conversion_factor:
                        parseFloat(
                            row.querySelector('.conv').value
                        ),

                    unit_price:
                        parseFloat(
                            row.querySelector('.price').value
                        )
                });
            });

            if (!isValid) {
                btn.innerHTML =
                    '<i class="fas fa-check-circle mr-2"></i> Simpan & Update Stok';

                btn.disabled = false;
                return;
            }

            const payload = {
                invoice_number:
                document.getElementById('invoice_number').value,

                supplier_name:
                document.getElementById('supplier_name').value,

                purchase_date:
                document.getElementById('purchase_date').value,

                status: 'received',

                items: items
            };

            try {
                const response = await fetch(
                    '/api/supplier-purchases',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(payload)
                    }
                );

                const data = await response.json();

                if (response.ok) {
                    alert(
                        'Pembelian berhasil dicatat dan stok berhasil di-update!'
                    );

                    window.location.reload();

                } else {
                    alert(
                        'Gagal: ' +
                        (data.message ||
                            'Terjadi kesalahan validasi.')
                    );
                }

            } catch (error) {
                console.error(error);

                alert(
                    'Terjadi kesalahan jaringan saat menyimpan pembelian.'
                );

            } finally {
                btn.innerHTML =
                    '<i class="fas fa-check-circle mr-2"></i> Simpan & Update Stok';

                btn.disabled = false;
            }
        }
    </script>
</x-layout>
