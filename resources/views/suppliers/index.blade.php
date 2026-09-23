<x-layout title="Pembelian Supplier">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-ckb-primary">
                Input Pembelian Supplier
            </h2>
            <p class="text-sm text-gray-500">
                Catat nota belanja dan update stok secara otomatis
            </p>
        </div>
    </div>

    <form id="form-purchase" onsubmit="submitPurchase(event)">

        <x-supplier.invoice-info />

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                <h3 class="font-bold text-ckb-primary">Daftar Item Belanja</h3>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshPurchaseData()"
                            class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 focus:ring-1 focus:ring-ckb-accent focus:outline-none transition"
                            title="Refresh Data">
                        <i class="fas fa-sync-alt"></i>
                    </button>

                    <button type="button" onclick="addRow()"
                            class="text-sm font-semibold text-ckb-primary hover:text-ckb-accent bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200 transition">
                        + Tambah Baris
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="purchase-table" class="w-full text-left">
                    <thead>
                    <tr class="text-[10px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-2 w-64">Nama Barang *</th>
                        <th class="pb-2 w-32">Satuan Beli *</th>
                        <th class="pb-2 w-20">Jumlah *</th>
                        <th class="pb-2 w-44">Konversi</th>
                        <th class="pb-2 w-36">Harga Satuan *</th>
                        <th class="pb-2 w-36">Subtotal</th>
                        <th class="pb-2 w-10 text-center">Aksi</th>
                    </tr>
                    </thead>

                    <tbody id="item-list"></tbody>
                </table>
            </div>

            <x-supplier.summary-footer />
        </div>

    </form>

    <template id="row-template">
        <x-supplier.item-row />
    </template>


    <script>
        let inventoryItems = [];

        const $ = id => document.getElementById(id);
        const csrfToken = '{{ csrf_token() }}';
        const loginUrl = '{{ route('login') }}';

        document.addEventListener('DOMContentLoaded', async () => {
            if ($('purchase_date')) $('purchase_date').valueAsDate = new Date();

            await Promise.all([
                fetchInventoryItems(),
                fetchSupplierSuggestions()
            ]);

            addRow();
        });


        // API helper
        async function apiFetch(url, options = {}) {
            const method = (options.method || 'GET').toUpperCase();
            const headers = {
                Accept: 'application/json',
                ...(options.headers || {})
            };

            if (method !== 'GET') headers['X-CSRF-TOKEN'] = csrfToken;

            if (options.body && typeof options.body !== 'string') {
                headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(options.body);
            }

            const response = await fetch(url, {
                ...options,
                headers,
                credentials: 'same-origin'
            });

            let data;

            try {
                data = await response.json();
            } catch {
                data = {
                    message: 'Server mengirim respons yang tidak dapat diproses.'
                };
            }

            if (response.status === 401) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'Sesi Berakhir',
                    text: 'Sesi login kamu sudah berakhir. Silakan login kembali.',
                    confirmButtonText: 'Login',
                    confirmButtonColor: '#6B4423'
                });

                window.location.href = loginUrl;
                throw new Error('UNAUTHORIZED');
            }

            return { response, data };
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function validationMessage(data) {
            if (!data?.errors) {
                return data?.message || 'Data belum dapat diproses.';
            }

            return Object.values(data.errors)
                .flat()
                .map(escapeHtml)
                .join('<br>');
        }

        async function showApiError(data, title = 'Terjadi Kesalahan') {
            await Swal.fire({
                icon: 'error',
                title,
                html: validationMessage(data),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#6B4423'
            });
        }


        // Inventory data
        async function fetchInventoryItems() {
            try {
                const { response, data } =
                    await apiFetch('/api/inventory-items');

                if (!response.ok) {
                    await showApiError(data, 'Gagal Memuat Daftar Barang');
                    return;
                }

                inventoryItems = Array.isArray(data.data) ? data.data : [];
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') return;

                console.error(error);
                await showApiError({
                    message: 'Daftar barang tidak dapat dimuat.'
                });
            }
        }

        async function fetchSupplierSuggestions() {
                try {
                    const { response, data } = await apiFetch('/api/supplier-suggestions');
                    const dataList = document.getElementById('supplier-suggestions');

                    if(!dataList) return;

                    dataList.innerHTML = '';

                    if(!response.ok) {
                        console.log('err',data);
                        return;
                    }

                    const suppliers = Array.isArray(data.data) ? data.data : [];

                    dataList.innerHTML = suppliers
                        .map(supplier => `
                            <option value="${escapeHtml(supplier)}"></option>
                            `)
                        .join('');
                } catch (err) {
                    console.error(err);
                }

        }

        async function refreshPurchaseData() {
            const btn = document.querySelector(
                'button[title="Refresh Data"]'
            );

            const rows = [...document.querySelectorAll('.item-row')];

            const rowStates = rows.map(row => ({
                itemKey: row.querySelector('.item-select')?.value || '',
                packagingId: row.querySelector('.packaging-select')?.value || '',
                quantity: row.querySelector('.qty')?.value || '1',
                price: row.querySelector('.price')?.value || '0'
            }));

            if (btn) {
                btn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i>';

                btn.disabled = true;
            }

            try {
                await Promise.all([
                    fetchInventoryItems(),
                    fetchSupplierSuggestions()
                ]);

                rows.forEach((row, index) => {
                    const state = rowStates[index];
                    const itemSelect =
                        row.querySelector('.item-select');
                    populateSelectOptions(itemSelect);

                    if (state.itemKey) {
                        itemSelect.value = state.itemKey;
                        onItemChange(itemSelect);
                    }

                    const packagingSelect =
                        row.querySelector('.packaging-select');

                    if (
                        state.packagingId &&
                        [...packagingSelect.options]
                            .some(option => option.value === state.packagingId)
                    ) {
                        packagingSelect.value = state.packagingId;
                        onPackagingChange(packagingSelect);
                    }

                    row.querySelector('.qty').value = state.quantity;
                    row.querySelector('.price').value = state.price;

                    calculateRow(
                        row.querySelector('.price')
                    );
                });

                await Swal.fire({
                    icon: 'success',
                    title: 'Data Diperbarui',
                    text: 'Daftar barang dan supplier berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                });

            } catch (error) {
                if (error.message === 'UNAUTHORIZED') {
                    return;
                }
                console.error(error);
                await showApiError({
                    message:
                        'Data pembelian tidak dapat diperbarui.'
                });
            } finally {
                if (btn) {
                    btn.innerHTML =
                        '<i class="fas fa-sync-alt"></i>';
                    btn.disabled = false;
                }
            }
        }


        // Row helpers
        function populateSelectOptions(select) {
            const options = inventoryItems.map(item => {
                const hasPackaging = Array.isArray(item.packagings) &&
                    item.packagings.length;

                return `
                    <option
                        value="${escapeHtml(item.item_key)}"
                        data-raw-id="${escapeHtml(item.raw_material_id || '')}"
                        data-prod-id="${escapeHtml(item.product_id || '')}"
                        data-unit="${escapeHtml(item.base_unit)}">
                        ${escapeHtml(item.name)}
                        ${hasPackaging ? '' : ' — belum ada kemasan'}
                    </option>
                `;
            }).join('');

            select.innerHTML = `
                <option value="" disabled selected>-- Pilih Barang --</option>
                ${options}
            `;
        }

        function addRow() {
            const clone = $('row-template').content.cloneNode(true);
            populateSelectOptions(clone.querySelector('.item-select'));
            $('item-list').appendChild(clone);
            updateSummary();
        }

        function removeRow(btn) {
            const rows = document.querySelectorAll('.item-row');

            if (rows.length <= 1) {
                showApiError(
                    { message: 'Minimal harus ada satu item pembelian.' },
                    'Tidak Dapat Menghapus'
                );
                return;
            }

            btn.closest('.item-row').remove();
            updateSummary();
        }


        // Item selection
        function onItemChange(select) {
            const row = select.closest('.item-row');
            const item = inventoryItems.find(
                item => item.item_key === select.value
            );

            const packaging = row.querySelector('.packaging-select');
            const conversion = row.querySelector('.conversion-text');

            packaging.innerHTML =
                '<option value="" disabled selected>-- Pilih Satuan --</option>';
            packaging.disabled = true;
            conversion.innerHTML = 'Pilih satuan beli';
            row.dataset.itemType = item?.type || '';

            if (!item) return;

            const packagings = Array.isArray(item.packagings)
                ? item.packagings
                : [];

            if (!packagings.length) {
                conversion.innerHTML = `
                    <span class="text-red-500">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Belum ada kemasan
                    </span>
                `;
                return;
            }

            packaging.innerHTML += packagings.map(item => `
                <option
                    value="${escapeHtml(item.id)}"
                    data-conversion="${escapeHtml(item.conversion_factor)}"
                    data-unit="${escapeHtml(item.purchase_unit)}">
                    ${escapeHtml(item.purchase_unit)}
                </option>
            `).join('');

            packaging.disabled = false;
        }

        function onPackagingChange(select) {
            const row = select.closest('.item-row');
            const packaging = select.options[select.selectedIndex];
            const item = row.querySelector('.item-select');
            const selectedItem = item.options[item.selectedIndex];

            const conversion = Number(packaging.dataset.conversion);
            const purchaseUnit = packaging.dataset.unit;
            const baseUnit = selectedItem.dataset.unit;
            const text = row.querySelector('.conversion-text');

            if (!Number.isFinite(conversion) || conversion <= 0) {
                text.innerText = 'Konversi tidak tersedia';
                return;
            }

            text.innerHTML = `
                <span class="font-semibold text-ckb-primary">
                    1 ${escapeHtml(purchaseUnit)} =
                    ${conversion.toLocaleString('id-ID')}
                    ${escapeHtml(baseUnit)}
                </span>
            `;

            calculateRow(select);
        }


        // Calculation
        function calculateRow(element) {
            const row = element.closest('.item-row');
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const subtotal = qty * price;

            row.querySelector('.subtotal').value =
                `Rp ${subtotal.toLocaleString('id-ID')}`;

            row.dataset.subtotalValue = subtotal;
            updateSummary();
        }

        function updateSummary() {
            const rows = document.querySelectorAll('.item-row');

            const total = [...rows].reduce(
                (sum, row) => sum + parseFloat(row.dataset.subtotalValue || 0),
                0
            );

            $('grand-total').innerText =
                `Rp ${total.toLocaleString('id-ID')}`;

            $('item-count').innerText =
                `${rows.length} item · stok akan diperbarui otomatis`;
        }


        // Submit purchase
        async function submitPurchase(event) {
            event.preventDefault();

            const rows = document.querySelectorAll('.item-row');
            const items = [];
            let isValid = true;

            rows.forEach((row, index) => {
                const line = index + 1;
                const itemSelect = row.querySelector('.item-select');
                const packagingSelect = row.querySelector('.packaging-select');
                const selectedOption =
                    itemSelect.options[itemSelect.selectedIndex];

                const item = inventoryItems.find(
                    item => item.item_key === itemSelect.value
                );

                const quantity =
                    parseFloat(row.querySelector('.qty').value);

                const unitPrice =
                    parseFloat(row.querySelector('.price').value);

                if (!itemSelect.value) {
                    showApiError({
                        message: `Silakan pilih barang pada baris ke-${line}.`
                    }, 'Data Belum Lengkap');

                    isValid = false;
                    return;
                }

                if (!packagingSelect.value) {
                    showApiError({
                        message: `Silakan pilih satuan beli pada baris ke-${line}.`
                    }, 'Satuan Beli Belum Dipilih');

                    isValid = false;
                    return;
                }

                if (!Number.isFinite(quantity) || quantity <= 0) {
                    showApiError({
                        message: `Jumlah pada baris ke-${line} harus lebih besar dari 0.`
                    }, 'Jumlah Tidak Valid');

                    isValid = false;
                    return;
                }

                if (!Number.isFinite(unitPrice) || unitPrice < 0) {
                    showApiError({
                        message: `Harga satuan pada baris ke-${line} tidak valid.`
                    }, 'Harga Tidak Valid');

                    isValid = false;
                    return;
                }

                const packagingId = Number(packagingSelect.value);

                if (!Number.isInteger(packagingId) || packagingId <= 0) {
                    showApiError({
                        message: `Kemasan pada baris ke-${line} tidak valid.`
                    }, 'Kemasan Tidak Valid');

                    isValid = false;
                    return;
                }

                const isRawMaterial = !!selectedOption.dataset.rawId;
                const isProduct = !!selectedOption.dataset.prodId;

                items.push({
                    raw_material_id: isRawMaterial
                        ? Number(selectedOption.dataset.rawId)
                        : null,

                    raw_material_packaging_id: isRawMaterial
                        ? packagingId
                        : null,

                    product_id: isProduct
                        ? Number(selectedOption.dataset.prodId)
                        : null,

                    product_packaging_id: isProduct
                        ? packagingId
                        : null,

                    quantity,
                    unit_price: unitPrice
                });
            });

            if (!isValid) return;

            const payload = {
                invoice_number: $('invoice_number').value.trim(),
                supplier_name: $('supplier_name').value.trim(),
                purchase_date: $('purchase_date').value,
                status: 'received',
                items
            };

            const result = await Swal.fire({
                icon: 'question',
                title: 'Simpan Pembelian?',
                text: 'Pembelian akan dicatat dan stok akan langsung diperbarui.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Periksa Lagi',
                confirmButtonColor: '#6B4423',
                cancelButtonColor: '#6b7280'
            });

            if (!result.isConfirmed) return;

            const btn = $('btn-submit');
            btn.disabled = true;
            btn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

            try {
                const { response, data } = await apiFetch(
                    '/api/supplier-purchases',
                    {
                        method: 'POST',
                        body: payload
                    }
                );

                if (!response.ok) {
                    await showApiError(
                        data,
                        response.status === 422
                            ? 'Data Pembelian Belum Valid'
                            : 'Pembelian Gagal'
                    );
                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Pembelian Berhasil',
                    text: 'Pembelian telah dicatat dan stok berhasil diperbarui.',
                    confirmButtonText: 'Selesai',
                    confirmButtonColor: '#6B4423'
                });

                window.location.reload();
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') return;

                console.error(error);

                await showApiError({
                    message: 'Pembelian tidak dapat diproses. Silakan coba lagi.'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    '<i class="fas fa-check-circle mr-2"></i> Simpan & Update Stok';
            }
        }
    </script>

</x-layout>
