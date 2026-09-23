<x-layout title="Master Bahan Baku">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-ckb-primary">Master Bahan Baku</h2>
            <p class="text-sm text-gray-500" id="summary-text">Memuat ringkasan stok...</p>
        </div>
        <button onclick="openModal()" class="bg-ckb-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-ckb-accent transition">
            + Tambah Bahan Baku
        </button>
    </div>

    <!-- Alert Kritis (Dinamis) -->
    <div id="critical-alert" class="hidden bg-red-50 border border-red-200 text-ckb-error p-4 rounded-xl mb-6 flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <div>
                <p class="font-bold text-sm" id="alert-title">Bahan baku dalam kondisi kritis!</p>
                <p class="text-xs">Segera lakukan restok melalui Pembelian Supplier untuk menghindari gangguan operasional.</p>
            </div>
        </div>
        <a href="{{ route('supplier.index') }}" class="text-sm font-semibold underline hover:text-red-800">Buat PO Sekarang</a>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-ckb-primary">Inventori Bahan Baku</h3>
            <div class="flex gap-2">
                <input type="text" id="search-input" onkeyup="filterTable()" placeholder="Cari bahan..." class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none">
            </div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 uppercase tracking-wider">
                <th class="py-3 font-medium">ID</th>
                <th class="py-3 font-medium">Nama Bahan Baku</th>
                <th class="py-3 font-medium">Satuan Dasar</th>
                <th class="py-3 font-medium">Stok Saat Ini</th>
                <th class="py-3 font-medium">Status</th>
            </tr>
            </thead>
            <tbody id="raw-material-list">
            <tr>
                <td colspan="5" class="py-6 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data bahan baku...
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Bahan Baku -->
    <div id="modal-add" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-ckb-bg w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg font-bold text-ckb-primary">Daftarkan Bahan Baku Baru</h3>
                    <p class="text-xs text-gray-500">Isi detail bahan baku lalu simpan ke database.</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form id="form-raw-material" onsubmit="submitRawMaterial(event)" class="p-6">
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-ckb-primary mb-1">Nama Bahan Baku *</label>
                        <input type="text" id="rm_name" required placeholder="cth: Kopi Bubuk Arabica" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ckb-primary mb-1">Satuan Dasar *</label>
                        <select id="rm_unit" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none bg-white">
                            <option value="gram">gram</option>
                            <option value="ml">ml</option>
                            <option value="pcs">pcs</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" id="btn-save-rm" class="px-4 py-2 bg-ckb-primary text-white rounded-lg text-sm font-medium hover:bg-ckb-accent flex items-center">
                        Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let rawMaterialsData = [];

        document.addEventListener('DOMContentLoaded', fetchRawMaterials);

        async function fetchRawMaterials() {
            try {
                const response = await fetch('/api/raw-materials', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (response.ok && result.data) {
                    rawMaterialsData = result.data;
                    renderTable(rawMaterialsData);
                } else {
                    console.error('API Error:', response.status, result);
                }

            } catch (error) {
                console.error('Error fetching raw materials:', error);
            }
        }

        function renderTable(items) {
            const tbody = document.getElementById('raw-material-list');
            const alertBox = document.getElementById('critical-alert');
            const summaryText = document.getElementById('summary-text');

            if (items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada bahan baku terdaftar.</td></tr>`;
                summaryText.innerText = '0 bahan terdaftar';
                alertBox.classList.add('hidden');
                return;
            }

            let criticalCount = 0;

            tbody.innerHTML = items.map(item => {
                const stock = parseFloat(item.current_stock);
                const isCritical = stock <= 100;

                if (isCritical) criticalCount++;

                const statusBadge = isCritical
                    ? `<span class="text-red-700 bg-red-50 px-2 py-1 rounded text-xs font-semibold">● Kritis / Menipis</span>`
                    : `<span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs font-semibold">● Aman</span>`;

                return `
                <tr class="border-b border-gray-50 text-sm">
                    <td class="py-3 text-gray-400 text-xs font-mono">#RM-${item.id}</td>
                    <td class="py-3 text-gray-800 font-medium">${item.name}</td>
                    <td class="py-3 text-gray-500">
                        <span class="bg-gray-100 px-2 py-0.5 rounded text-xs">
                            ${item.unit_measurement}
                        </span>
                    </td>
                    <td class="py-3 font-bold text-ckb-primary">
                        ${stock.toLocaleString('id-ID')} ${item.unit_measurement}
                    </td>
                    <td class="py-3">${statusBadge}</td>
                </tr>
            `;
            }).join('');

            summaryText.innerText =
                `${items.length} bahan terdaftar · ${criticalCount} perlu perhatian`;

            if (criticalCount > 0) {
                alertBox.classList.remove('hidden');

                document.getElementById('alert-title').innerText =
                    `${criticalCount} bahan baku dalam kondisi kritis!`;
            } else {
                alertBox.classList.add('hidden');
            }
        }

        function filterTable() {
            const query = document
                .getElementById('search-input')
                .value
                .toLowerCase();

            const filtered = rawMaterialsData.filter(i =>
                i.name.toLowerCase().includes(query)
            );

            renderTable(filtered);
        }

        function openModal() {
            document.getElementById('modal-add').classList.remove('hidden');
            document.getElementById('modal-add').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modal-add').classList.add('hidden');
            document.getElementById('modal-add').classList.remove('flex');
            document.getElementById('form-raw-material').reset();
        }

        async function submitRawMaterial(e) {
            e.preventDefault();

            const btn = document.getElementById('btn-save-rm');

            btn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

            btn.disabled = true;

            const payload = {
                name: document.getElementById('rm_name').value,
                unit_measurement: document.getElementById('rm_unit').value
            };

            try {
                const response = await fetch('/api/raw-materials', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    closeModal();
                    await fetchRawMaterials();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                }

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');

            } finally {
                btn.innerHTML = 'Simpan Bahan Baku';
                btn.disabled = false;
            }
        }
    </script>
</x-layout>
