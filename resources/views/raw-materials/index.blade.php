<x-layout title="Master Bahan Baku">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-ckb-primary">Master Bahan Baku</h2>
            <p id="summary-text" class="text-sm text-gray-500">Memuat ringkasan stok...</p>
        </div>

        <button type="button" onclick="openAddMaterialModal()"
                class="bg-ckb-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-ckb-accent transition">
            + Tambah Bahan Baku
        </button>
    </div>

    {{-- Critical stock alert --}}
    <div id="critical-alert"
         class="hidden bg-red-50 border border-red-200 text-ckb-error p-4 rounded-xl mb-6 flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <div>
                <p id="alert-title" class="font-bold text-sm">
                    Bahan baku dalam kondisi kritis!
                </p>
                <p class="text-xs">
                    Segera lakukan restok melalui Pembelian Supplier untuk menghindari gangguan operasional.
                </p>
            </div>
        </div>

        <a href="{{ route('supplier.index') }}"
           class="text-sm font-semibold underline hover:text-red-800">
            Buat PO Sekarang
        </a>
    </div>

    {{-- Material table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-ckb-primary">Inventori Bahan Baku</h3>

            <div class="flex items-center gap-2">
                <button type="button" onclick="fetchRawMaterials()"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 focus:ring-1 focus:ring-ckb-accent focus:outline-none transition"
                        title="Refresh Data">
                    <i class="fas fa-sync-alt"></i>
                </button>

                <input id="search-input" type="text" oninput="filterTable()"
                       placeholder="Cari bahan..."
                       class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="text-xs text-gray-400 border-b border-gray-100 uppercase tracking-wider">
                    <th class="py-3 font-medium">ID</th>
                    <th class="py-3 font-medium">Nama Bahan Baku</th>
                    <th class="py-3 font-medium">Satuan Dasar</th>
                    <th class="py-3 font-medium">Stok Saat Ini</th>
                    <th class="py-3 font-medium">Status</th>
                    <th class="py-3 font-medium text-right">Aksi</th>
                </tr>
                </thead>

                <tbody id="raw-material-list">
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Memuat data bahan baku...
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Material modal --}}
    <div id="material-modal"
         class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

        <div class="bg-ckb-bg w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                <div>
                    <h3 id="material-modal-title" class="text-lg font-bold text-ckb-primary">
                        Daftarkan Bahan Baku Baru
                    </h3>
                    <p class="text-xs text-gray-500">Isi informasi dasar bahan baku.</p>
                </div>

                <button type="button" onclick="closeMaterialModal()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form
                id="material-form"
                onsubmit="submitMaterial(event)"
                class="p-6"
            >
                <input
                    id="material-id"
                    type="hidden"
                >

                <!-- Informasi dasar -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-ckb-primary mb-1">
                            Nama Bahan Baku *
                        </label>
                        <input
                            id="rm_name"
                            type="text"
                            required
                            maxlength="100"
                            placeholder="cth: Susu UHT"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none bg-white"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ckb-primary mb-1">
                            Satuan Dasar *
                        </label>
                        <select
                            id="rm_unit"
                            required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none bg-white"
                        >
                            <option value="gram">gram</option>
                            <option value="ml">ml</option>
                            <option value="pcs">pcs</option>
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">
                            Satuan ini digunakan untuk menyimpan stok.
                        </p>
                    </div>
                </div>

                <!-- Packaging -->
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h4 class="text-sm font-bold text-ckb-primary">
                                Kemasan Pembelian
                            </h4>
                            <p class="text-[11px] text-gray-400">
                                Tentukan satuan pembelian dan jumlah satuan dasar di dalamnya.
                            </p>
                        </div>

                        <button
                            type="button"
                            onclick="addMaterialPackagingRow()"
                            class="text-xs font-semibold text-ckb-primary hover:text-ckb-accent"
                        >
                            + Tambah Kemasan
                        </button>
                    </div>
                    <div
                        id="material-packaging-list"
                        class="space-y-3"
                    >
                        <!-- Packaging row dibuat via JS -->
                    </div>
                    <div
                        id="material-packaging-empty"
                        class="border border-dashed border-gray-300 rounded-lg p-4 text-center"
                    >
                        <i class="fas fa-box-open text-gray-300 text-xl mb-2"></i>
                        <p class="text-xs text-gray-400">Belum ada kemasan pembelian.</p>

                        <button
                            type="button"
                            onclick="addMaterialPackagingRow()"
                            class="text-xs text-ckb-primary font-semibold mt-1 hover:underline"
                        >Tambahkan kemasan</button>
                    </div>
                </div>

                <!-- Action -->
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        onclick="closeMaterialModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50"
                    >Batal</button>

                    <button
                        type="submit"
                        id="btn-save-material"
                        class="px-4 py-2 bg-ckb-primary text-white rounded-lg text-sm font-medium hover:bg-ckb-accent flex items-center"
                    >Simpan Bahan Baku</button>
                </div>
            </form>

            <template id="material-packaging-template">
                <div class="material-packaging-row border border-gray-200 rounded-lg p-3">
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">
                                Satuan Beli *
                            </label>
                            <input
                                type="text"
                                class="material-packaging-unit w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none"
                                placeholder="cth: kardus"
                                maxlength="20"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">
                                Isi dalam Satuan Dasar *
                            </label>
                            <input
                                type="number"
                                class="material-packaging-conversion w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none"
                                min="0.001"
                                step="0.001"
                                placeholder="cth: 12000"
                                oninput="updateMaterialPackagingPreview(this)"
                                required
                            >
                        </div>

                        <button
                            type="button"
                            onclick="removeMaterialPackagingRow(this)"
                            class="h-[38px] w-[38px] rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                            title="Hapus kemasan"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <p class="material-packaging-preview text-[11px] text-gray-400 mt-2">
                        Masukkan jumlah konversi.
                    </p>
                </div>
            </template>

        </div>
    </div>

    {{-- Packaging modal --}}
    <div id="packaging-modal"
         class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

        <div class="bg-ckb-bg w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-start">
                <div>
                    <h3 id="packaging-modal-title" class="text-lg font-bold text-ckb-primary">
                        Kemasan Bahan Baku
                    </h3>
                    <p id="packaging-modal-description" class="text-xs text-gray-500 mt-1">
                        Kelola satuan pembelian bahan baku.
                    </p>
                </div>

                <button type="button" onclick="closePackagingModal()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                {{-- Packaging form --}}
                <form id="packaging-form" onsubmit="submitPackaging(event)"
                      class="bg-white border border-gray-200 rounded-xl p-4 mb-5">

                    <input id="packaging-id" type="hidden">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-ckb-primary mb-1">
                                Satuan Beli *
                            </label>
                            <input id="packaging-unit" type="text" maxlength="20" required
                                   placeholder="cth: kardus"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ckb-primary mb-1">
                                Konversi *
                            </label>
                            <input id="packaging-conversion" type="number" min="0.001" step="0.001"
                                   required placeholder="cth: 12000"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ckb-primary mb-1">
                                Satuan Dasar
                            </label>
                            <div id="packaging-base-unit"
                                 class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500">
                                -
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" id="btn-cancel-packaging-edit"
                                onclick="cancelPackagingEdit()"
                                class="hidden px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            Batal Edit
                        </button>

                        <button type="submit" id="btn-save-packaging"
                                class="px-4 py-2 bg-ckb-primary text-white rounded-lg text-sm font-medium hover:bg-ckb-accent">
                            + Tambah Kemasan
                        </button>
                    </div>
                </form>

                {{-- Packaging list --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h4 class="font-semibold text-sm text-ckb-primary">Daftar Kemasan</h4>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                            <tr class="text-[10px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-4 py-3">Satuan Beli</th>
                                <th class="px-4 py-3">Konversi</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                            </thead>

                            <tbody id="packaging-list">
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">
                                    Memuat kemasan...
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rawMaterialsData = [];
        let activePackagingMaterial = null;
        let packagingData = [];

        const $ = id => document.getElementById(id);
        const csrfToken = '{{ csrf_token() }}';
        const loginUrl = '{{ route('login') }}';

        document.addEventListener('DOMContentLoaded', fetchRawMaterials);

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

        // Shared helpers
        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function validationMessage(data) {
            if (!data?.errors) return data?.message || 'Data tidak dapat diproses.';

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

        function toggleModal(id, show) {
            const modal = $(id);
            modal.classList.toggle('hidden', !show);
            modal.classList.toggle('flex', show);
        }

        function findById(items, id) {
            return items.find(item => Number(item.id) === Number(id));
        }

        function setButtonLoading(button, loading, text = 'Menyimpan...') {
            button.disabled = loading;
            button.innerHTML = loading
                ? `<i class="fas fa-spinner fa-spin mr-2"></i> ${text}`
                : button.dataset.defaultText;
        }

        // Material list
        async function fetchRawMaterials() {
            try {
                const { response, data } = await apiFetch('/api/raw-materials');

                if (!response.ok) {
                    await showApiError(data, 'Gagal Memuat Bahan Baku');
                    return;
                }

                rawMaterialsData = Array.isArray(data.data) ? data.data : [];
                renderTable(rawMaterialsData);
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') return;

                console.error(error);

                await Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Bahan baku tidak dapat dimuat. Periksa koneksi lalu coba lagi.',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#6B4423'
                });
            }
        }

        function renderTable(items) {
            const tbody = $('raw-material-list');
            const alertBox = $('critical-alert');
            const summary = $('summary-text');

            if (!items.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">
                            Belum ada bahan baku terdaftar.
                        </td>
                    </tr>
                `;

                summary.innerText = '0 bahan terdaftar';
                alertBox.classList.add('hidden');
                return;
            }

            let criticalCount = 0;

            tbody.innerHTML = items.map(item => {
                const stock = parseFloat(item.current_stock) || 0;
                const isCritical = stock <= 100;

                if (isCritical) criticalCount++;

                const status = isCritical
                    ? `<span class="text-red-700 bg-red-50 px-2 py-1 rounded text-xs font-semibold">
                        ● Kritis / Menipis
                       </span>`
                    : `<span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs font-semibold">
                        ● Aman
                       </span>`;

                return `
                    <tr class="border-b border-gray-50 text-sm">
                        <td class="py-3 text-gray-400 text-xs font-mono">
                            #RM-${escapeHtml(item.id)}
                        </td>

                        <td class="py-3 text-gray-800 font-medium">
                            ${escapeHtml(item.name)}
                        </td>

                        <td class="py-3 text-gray-500">
                            <span class="bg-gray-100 px-2 py-0.5 rounded text-xs">
                                ${escapeHtml(item.unit_measurement)}
                            </span>
                        </td>

                        <td class="py-3 font-bold text-ckb-primary">
                            ${stock.toLocaleString('id-ID')}
                            ${escapeHtml(item.unit_measurement)}
                        </td>

                        <td class="py-3">${status}</td>

                        <td class="py-3">
                            <div class="flex justify-end gap-2">
                                <button type="button"
                                    onclick="openPackagingModal(${Number(item.id)})"
                                    class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold"
                                    title="Kelola Kemasan">
                                    <i class="fas fa-box mr-1"></i>Kemasan
                                </button>

                                <button type="button"
                                    onclick="openEditMaterialModal(${Number(item.id)})"
                                    class="px-2.5 py-1.5 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-semibold"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button"
                                    onclick="deleteMaterial(${Number(item.id)})"
                                    class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            summary.innerText =
                `${items.length} bahan terdaftar · ${criticalCount} perlu perhatian`;

            alertBox.classList.toggle('hidden', criticalCount === 0);

            if (criticalCount) {
                $('alert-title').innerText =
                    `${criticalCount} bahan baku dalam kondisi kritis!`;
            }
        }

        function filterTable() {
            const query = $('search-input').value.trim().toLowerCase();

            renderTable(
                rawMaterialsData.filter(item =>
                    item.name.toLowerCase().includes(query)
                )
            );
        }

        // Material CRUD
        function openAddMaterialModal() {

            $('material-form').reset();

            $('material-id').value = '';

            $('material-modal-title').innerText =
                'Daftarkan Bahan Baku Baru';


            const button =
                $('btn-save-material');

            button.innerText =
                'Simpan Bahan Baku';

            button.dataset.defaultText =
                'Simpan Bahan Baku';


            /*
             * Bersihkan packaging lama
             */
            $('material-packaging-list').innerHTML = '';

            refreshMaterialPackagingEmptyState();


            toggleModal(
                'material-modal',
                true
            );
        }

        async function openEditMaterialModal(id) {

            const item =
                findById(
                    rawMaterialsData,
                    id
                );


            if (!item) {

                await showApiError({
                    message:
                        'Data bahan baku tidak ditemukan.'
                });

                return;
            }


            $('material-id').value =
                item.id;

            $('rm_name').value =
                item.name;

            $('rm_unit').value =
                item.unit_measurement;


            $('material-modal-title').innerText =
                'Edit Bahan Baku';


            const button =
                $('btn-save-material');

            button.innerText =
                'Simpan Perubahan';

            button.dataset.defaultText =
                'Simpan Perubahan';


            /*
             * Bersihkan packaging
             */
            $('material-packaging-list').innerHTML = '';

            refreshMaterialPackagingEmptyState();


            toggleModal(
                'material-modal',
                true
            );


            /*
             * Load packaging
             */
            try {

                const {
                    response,
                    data
                } = await apiFetch(
                    `/api/raw-materials/${id}/packagings`
                );


                if (!response.ok) {

                    await showApiError(
                        data,
                        'Gagal Memuat Kemasan'
                    );

                    return;
                }


                const packagings =
                    Array.isArray(data.data)
                        ? data.data
                        : [];


                packagings.forEach(
                    packaging =>
                        addMaterialPackagingRow(
                            packaging
                        )
                );


                refreshMaterialPackagingEmptyState();


            } catch (error) {

                if (
                    error.message ===
                    'UNAUTHORIZED'
                ) {
                    return;
                }


                console.error(error);


                await showApiError({
                    message:
                        'Konfigurasi kemasan tidak dapat dimuat.'
                });

            }
        }

        function closeMaterialModal() {
            toggleModal('material-modal', false);
        }

        async function submitMaterial(event) {
            event.preventDefault();

            const button =
                $('btn-save-material');

            const id =
                $('material-id').value;

            const packagingRows =
                [
                    ...document.querySelectorAll(
                        '.material-packaging-row'
                    )
                ];

            const payload = {
                name:
                    $('rm_name').value.trim(),

                unit_measurement:
                $('rm_unit').value
            };


            /*
             * Validasi packaging
             */
            if (packagingRows.length === 0) {

                await Swal.fire({
                    icon: 'warning',
                    title: 'Kemasan Belum Diatur',
                    text: 'Tambahkan minimal satu kemasan pembelian agar bahan baku dapat digunakan pada Pembelian Supplier.',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#6B4423'
                });

                return;
            }


            const packagings = [];
            let packagingValid = true;


            packagingRows.forEach((row, index) => {

                const line =
                    index + 1;

                const purchaseUnit =
                    row.querySelector(
                        '.material-packaging-unit'
                    ).value.trim();

                const conversionFactor =
                    Number(
                        row.querySelector(
                            '.material-packaging-conversion'
                        ).value
                    );


                if (!purchaseUnit) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Satuan Beli Belum Diisi',
                        text:
                            `Satuan beli pada kemasan ke-${line} wajib diisi.`,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#6B4423'
                    });

                    packagingValid = false;

                    return;
                }


                if (
                    !Number.isFinite(conversionFactor) ||
                    conversionFactor <= 0
                ) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Konversi Tidak Valid',
                        text:
                            `Nilai konversi pada kemasan ke-${line} harus lebih besar dari 0.`,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#6B4423'
                    });

                    packagingValid = false;

                    return;
                }


                packagings.push({
                    purchase_unit: purchaseUnit,
                    conversion_factor: conversionFactor,
                    is_active: true
                });

            });


            if (!packagingValid) {
                return;
            }


            /*
             * Cegah satuan beli duplikat
             */
            const units =
                packagings.map(
                    packaging =>
                        packaging.purchase_unit.toLowerCase()
                );

            if (new Set(units).size !== units.length) {

                await Swal.fire({
                    icon: 'warning',
                    title: 'Satuan Beli Duplikat',
                    text: 'Satuan beli yang sama tidak boleh didaftarkan dua kali.',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#6B4423'
                });

                return;
            }


            button.disabled = true;

            button.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';


            try {

                /*
                 * EDIT
                 */
                if (id) {

                    const {
                        response,
                        data
                    } = await apiFetch(
                        `/api/raw-materials/${id}`,
                        {
                            method: 'PUT',
                            body: payload
                        }
                    );


                    if (!response.ok) {

                        await showApiError(
                            data,
                            'Gagal Mengubah Bahan'
                        );

                        return;
                    }


                    /*
                     * Untuk update packaging,
                     * sementara kita hanya mengedit/tambah.
                     */
                    const existingPackagingResponse =
                        await apiFetch(
                            `/api/raw-materials/${id}/packagings`
                        );


                    if (!existingPackagingResponse.response.ok) {

                        await showApiError(
                            existingPackagingResponse.data,
                            'Gagal Memuat Kemasan'
                        );

                        return;
                    }


                    const existingPackagings =
                        Array.isArray(
                            existingPackagingResponse.data.data
                        )
                            ? existingPackagingResponse.data.data
                            : [];


                    /*
                     * Sinkronisasi sederhana:
                     *
                     * - Packaging lama dengan ID yang sama → PUT
                     * - Packaging baru → POST
                     *
                     * Penghapusan packaging tetap dilakukan
                     * lewat modal kelola packaging.
                     */


                    for (
                        let i = 0;
                        i < packagings.length;
                        i++
                    ) {

                        const packaging =
                            packagings[i];

                        const existing =
                            existingPackagings.find(
                                item =>
                                    item.purchase_unit
                                        .toLowerCase() ===
                                    packaging.purchase_unit
                                        .toLowerCase()
                            );


                        const packagingUrl =
                            `/api/raw-materials/${id}/packagings`;


                        const result =
                            await apiFetch(
                                existing
                                    ? `${packagingUrl}/${existing.id}`
                                    : packagingUrl,
                                {
                                    method:
                                        existing
                                            ? 'PUT'
                                            : 'POST',

                                    body: packaging
                                }
                            );


                        if (!result.response.ok) {

                            await showApiError(
                                result.data,
                                existing
                                    ? 'Gagal Mengubah Kemasan'
                                    : 'Gagal Menambahkan Kemasan'
                            );

                            return;
                        }

                    }


                    closeMaterialModal();


                    await Swal.fire({
                        icon: 'success',
                        title: 'Bahan Baku Diperbarui',
                        text:
                            'Data bahan dan konfigurasi kemasan berhasil diperbarui.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#6B4423'
                    });


                    await fetchRawMaterials();


                    return;
                }


                /*
                 * CREATE RAW MATERIAL
                 */
                const {
                    response,
                    data
                } = await apiFetch(
                    '/api/raw-materials',
                    {
                        method: 'POST',
                        body: payload
                    }
                );


                if (!response.ok) {

                    await showApiError(
                        data,
                        'Gagal Menambahkan Bahan'
                    );

                    return;
                }


                const rawMaterialId =
                    data.data?.id;


                if (!rawMaterialId) {

                    await showApiError({
                        message:
                            'Bahan berhasil dibuat tetapi ID bahan tidak ditemukan.'
                    });

                    return;
                }


                /*
                 * CREATE PACKAGING
                 */
                for (
                    const packaging of packagings
                    ) {

                    const packagingResponse =
                        await apiFetch(
                            `/api/raw-materials/${rawMaterialId}/packagings`,
                            {
                                method: 'POST',
                                body: packaging
                            }
                        );


                    if (!packagingResponse.response.ok) {

                        /*
                         * Bahan sudah dibuat,
                         * tetapi packaging gagal.
                         */
                        await showApiError(
                            packagingResponse.data,
                            'Bahan Berhasil, Kemasan Gagal'
                        );

                        return;
                    }
                }


                closeMaterialModal();


                await Swal.fire({
                    icon: 'success',
                    title: 'Bahan Baku Ditambahkan',
                    text:
                        'Bahan baku dan kemasan pembelian berhasil disimpan.',
                    confirmButtonText: 'Selesai',
                    confirmButtonColor: '#6B4423'
                });


                await fetchRawMaterials();


            } catch (error) {

                if (
                    error.message ===
                    'UNAUTHORIZED'
                ) {
                    return;
                }


                console.error(error);


                await showApiError({
                    message:
                        'Bahan baku tidak dapat disimpan. Silakan coba lagi.'
                });


            } finally {

                button.disabled = false;

                button.innerHTML =
                    id
                        ? 'Simpan Perubahan'
                        : 'Simpan Bahan Baku';

            }
        }

        async function deleteMaterial(id) {
            const item = findById(rawMaterialsData, id);
            if (!item) return;

            const result = await Swal.fire({
                icon: 'warning',
                title: 'Hapus Bahan Baku?',
                html: `
                    Bahan <strong>${escapeHtml(item.name)}</strong> akan dihapus.
                    <br><br>
                    <span class="text-red-600 text-sm">
                        Pastikan bahan ini memang tidak lagi digunakan.
                    </span>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
            });

            if (!result.isConfirmed) return;

            try {
                const { response, data } = await apiFetch(
                    `/api/raw-materials/${id}`,
                    { method: 'DELETE' }
                );

                if (!response.ok) {
                    await showApiError(data, 'Bahan Tidak Dapat Dihapus');
                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Bahan Baku Dihapus',
                    text: 'Data bahan baku berhasil dihapus.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6B4423'
                });

                await fetchRawMaterials();
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') return;

                console.error(error);

                await showApiError({
                    message: 'Bahan baku tidak dapat dihapus.'
                });
            }
        }

        // Packaging CRUD
        async function openPackagingModal(id) {
            const item = findById(rawMaterialsData, id);

            if (!item) {
                await showApiError({ message: 'Bahan baku tidak ditemukan.' });
                return;
            }

            activePackagingMaterial = item;

            $('packaging-modal-title').innerText = `Kemasan · ${item.name}`;
            $('packaging-modal-description').innerText =
                `Atur satuan pembelian untuk ${item.name}.`;
            $('packaging-base-unit').innerText = item.unit_measurement;

            resetPackagingForm();
            toggleModal('packaging-modal', true);

            await fetchPackagings(id);
        }

        function closePackagingModal() {
            toggleModal('packaging-modal', false);
            activePackagingMaterial = null;
            packagingData = [];
            resetPackagingForm();
        }

        function resetPackagingForm() {
            $('packaging-form').reset();
            $('packaging-id').value = '';

            const button = $('btn-save-packaging');
            button.innerText = '+ Tambah Kemasan';
            button.dataset.defaultText = '+ Tambah Kemasan';

            $('btn-cancel-packaging-edit').classList.add('hidden');

            if (activePackagingMaterial) {
                $('packaging-base-unit').innerText =
                    activePackagingMaterial.unit_measurement;
            }
        }

        function cancelPackagingEdit() {
            resetPackagingForm();
        }

        async function fetchPackagings(rawMaterialId) {
            const tbody = $('packaging-list');

            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Memuat kemasan...
                    </td>
                </tr>
            `;

            try {
                const { response, data } = await apiFetch(
                    `/api/raw-materials/${rawMaterialId}/packagings`
                );

                if (!response.ok) {
                    await showApiError(data, 'Gagal Memuat Kemasan');
                    return;
                }

                packagingData = Array.isArray(data.data) ? data.data : [];
                renderPackagingList();
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') return;

                console.error(error);

                await showApiError({
                    message: 'Daftar kemasan tidak dapat dimuat.'
                });
            }
        }

        function renderPackagingList() {
            const tbody = $('packaging-list');

            if (!packagingData.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-400">
                            <i class="fas fa-box-open text-2xl mb-2"></i>
                            <p class="text-sm">Belum ada kemasan.</p>
                            <p class="text-xs mt-1">
                                Tambahkan kemasan agar bahan ini bisa digunakan pada Pembelian Supplier.
                            </p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = packagingData.map(packaging => {
                const conversion = Number(packaging.conversion_factor);

                const status = packaging.is_active
                    ? `<span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs font-semibold">
                        Aktif
                       </span>`
                    : `<span class="text-gray-600 bg-gray-100 px-2 py-1 rounded text-xs font-semibold">
                        Nonaktif
                       </span>`;

                return `
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-medium text-sm text-gray-800">
                                ${escapeHtml(packaging.purchase_unit)}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-ckb-primary">
                                ${conversion.toLocaleString('id-ID')}
                                ${escapeHtml(activePackagingMaterial?.unit_measurement || '')}
                            </span>
                            <span class="text-xs text-gray-400 ml-1">
                                per 1 ${escapeHtml(packaging.purchase_unit)}
                            </span>
                        </td>

                        <td class="px-4 py-3">${status}</td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button type="button"
                                    onclick="editPackaging(${Number(packaging.id)})"
                                    class="px-2.5 py-1.5 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button"
                                    onclick="deletePackaging(${Number(packaging.id)})"
                                    class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function editPackaging(id) {
            const packaging = findById(packagingData, id);
            if (!packaging) return;

            $('packaging-id').value = packaging.id;
            $('packaging-unit').value = packaging.purchase_unit;
            $('packaging-conversion').value = packaging.conversion_factor;

            const button = $('btn-save-packaging');
            button.innerText = 'Simpan Perubahan';
            button.dataset.defaultText = 'Simpan Perubahan';

            $('btn-cancel-packaging-edit').classList.remove('hidden');
            $('packaging-unit').focus();
        }

        async function submitPackaging(event) {
            event.preventDefault();

            if (!activePackagingMaterial) {
                await showApiError({
                    message: 'Bahan baku belum dipilih.'
                });

                return;
            }

            const button = $('btn-save-packaging');
            const id = $('packaging-id').value;

            const purchaseUnit =
                $('packaging-unit').value.trim();

            const conversionFactor =
                Number($('packaging-conversion').value);

            if (!purchaseUnit) {
                await showApiError({
                    message: 'Satuan beli wajib diisi.'
                }, 'Data Belum Lengkap');

                $('packaging-unit').focus();

                return;
            }

            if (!Number.isFinite(conversionFactor) || conversionFactor <= 0) {
                await showApiError({
                    message: 'Nilai konversi harus lebih besar dari 0.'
                }, 'Konversi Tidak Valid');
                $('packaging-conversion').focus();
                return;
            }

            const payload = {
                purchase_unit: purchaseUnit,
                conversion_factor: conversionFactor,
                is_active: true
            };

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

            try {
                const baseUrl = `/api/raw-materials/${activePackagingMaterial.id}/packagings`;
                const url = id ? `${baseUrl}/${id}` : baseUrl;
                const method = id ? 'PUT' : 'POST';
                const {response, data} = await apiFetch(url, {method, body: payload});

                if (!response.ok) {
                    await showApiError(
                        data,
                        id
                            ? 'Gagal Mengubah Kemasan'
                            : 'Gagal Menambahkan Kemasan'
                    );
                    return;
                }

                resetPackagingForm();

                await fetchPackagings(
                    activePackagingMaterial.id
                );

                await Swal.fire({
                    icon: 'success',
                    title: id
                        ? 'Kemasan Diperbarui'
                        : 'Kemasan Ditambahkan',
                    text: id
                        ? 'Konfigurasi kemasan berhasil diperbarui.'
                        : 'Kemasan berhasil ditambahkan.',

                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6B4423'
                });
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') {
                    return;
                }
                console.error(error);

                await showApiError({
                    message:
                        'Kemasan tidak dapat disimpan. Silakan coba lagi.'
                });
            } finally {
                button.disabled = false;
                button.innerHTML =
                    id
                        ? 'Simpan Perubahan'
                        : '+ Tambah Kemasan';
            }
        }

        async function deletePackaging(id) {

            const packaging =
                findById(packagingData, id);

            if (!packaging || !activePackagingMaterial) {
                return;
            }

            const result = await Swal.fire({
                icon: 'warning',
                title: 'Hapus Kemasan?',
                html: `
            <p>
                Kamu akan menghapus kemasan
                <strong>
                    ${escapeHtml(packaging.purchase_unit)}
                </strong>.
            </p>

            <p class="text-sm text-gray-500 mt-3">
                Jika kemasan sudah pernah digunakan dalam transaksi,
                sistem akan menolaknya demi menjaga histori pembelian.
            </p>
        `,

                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'

            });

            if (!result.isConfirmed) {return;}

            try {
                const {response, data} = await apiFetch(`/api/raw-materials/${activePackagingMaterial.id}/packagings/${id}`,
                    {method: 'DELETE'}
                );

                if (!response.ok) {
                    await showApiError(data, 'Kemasan Tidak Dapat Dihapus');
                    return;
                }

                await fetchPackagings(activePackagingMaterial.id);

                await Swal.fire({
                    icon: 'success',
                    title: 'Kemasan Dihapus',
                    text: 'Kemasan berhasil dihapus.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6B4423'
                });
            } catch (error) {
                if (error.message === 'UNAUTHORIZED') {
                    return;
                }

                console.error(error);
                await showApiError({message: 'Kemasan tidak dapat dihapus.'});
            }
        }

        function refreshMaterialPackagingEmptyState() {
            const list = $('material-packaging-list');
            const empty = $('material-packaging-empty');

            const hasRows =
                list.querySelectorAll('.material-packaging-row').length > 0;

            empty.classList.toggle('hidden', hasRows);
        }


        function addMaterialPackagingRow(data = null) {
            const template =
                $('material-packaging-template');

            const clone =
                template.content.cloneNode(true);

            const row =
                clone.querySelector('.material-packaging-row');

            const unitInput =
                row.querySelector('.material-packaging-unit');

            const conversionInput =
                row.querySelector('.material-packaging-conversion');

            if (data) {
                unitInput.value =
                    data.purchase_unit || '';

                conversionInput.value =
                    data.conversion_factor || '';

                updateMaterialPackagingPreview(
                    conversionInput
                );
            }

            $('material-packaging-list')
                .appendChild(clone);

            refreshMaterialPackagingEmptyState();

            return row;
        }


        function removeMaterialPackagingRow(button) {
            button
                .closest('.material-packaging-row')
                .remove();

            refreshMaterialPackagingEmptyState();
        }


        function updateMaterialPackagingPreview(input) {
            const row =
                input.closest('.material-packaging-row');

            const unit =
                row.querySelector(
                    '.material-packaging-unit'
                ).value.trim();

            const conversion =
                Number(input.value);

            const baseUnit =
                $('rm_unit').value;

            const preview =
                row.querySelector(
                    '.material-packaging-preview'
                );

            if (
                !unit ||
                !Number.isFinite(conversion) ||
                conversion <= 0
            ) {
                preview.innerText =
                    'Masukkan jumlah konversi.';

                return;
            }

            preview.innerHTML = `
        <span class="font-semibold text-ckb-primary">
            1 ${escapeHtml(unit)}
            =
            ${conversion.toLocaleString('id-ID')}
            ${escapeHtml(baseUnit)}
        </span>
    `;
        }
    </script>

</x-layout>
