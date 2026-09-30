import { apiFetch } from "../utils/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const cardTotal = document.getElementById("card-total-materials");
    const cardNormal = document.getElementById("card-normal-count");
    const cardCritical = document.getElementById("card-critical-count");
    const cardEmpty = document.getElementById("card-empty-count");

    const listEl = document.getElementById("stock-materials-list");
    const searchInput = document.getElementById("stock-search");
    const filterStatusSelect = document.getElementById("stock-status-filter");
    const btnRefresh = document.getElementById("btn-refresh-stock");

    let allMaterials = [];

    async function loadStockReport() {
        listEl.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat laporan stok...
                </td>
            </tr>
        `;

        try {
            const { data } = await apiFetch('/api/reports/stock');
            const summary = data.data.summary;
            allMaterials = data.data.materials || [];

            cardTotal.textContent = summary.total_materials;
            cardNormal.textContent = summary.normal_count;
            cardCritical.textContent = summary.critical_count;
            cardEmpty.textContent = summary.out_of_stock_count;

            renderMaterials();
        } catch (err) {
            listEl.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-red-500">
                        Gagal memuat data laporan stok.
                    </td>
                </tr>
            `;
        }
    }

    function renderMaterials() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedStatus = filterStatusSelect.value;

        const filtered = allMaterials.filter(mat => {
            const matchQuery = mat.name.toLowerCase().includes(query);
            const matchStatus = selectedStatus ? mat.status === selectedStatus : true;
            return matchQuery && matchStatus;
        });

        if (filtered.length === 0) {
            listEl.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                        Tidak ada bahan baku yang sesuai.
                    </td>
                </tr>
            `;
            return;
        }

        listEl.innerHTML = filtered.map(mat => {
            let statusBadge = '';
            if (mat.status === 'habis') {
                statusBadge = '<span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-600">STOK HABIS</span>';
            } else if (mat.status === 'kritis') {
                statusBadge = '<span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">STOK KRITIS</span>';
            } else {
                statusBadge = '<span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">NORMAL</span>';
            }

            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition text-sm">
                    <td class="px-4 py-3 font-semibold text-gray-800">${mat.name}</td>
                    <td class="px-4 py-3 font-bold text-ckb-primary">${mat.stock} ${mat.unit}</td>
                    <td class="px-4 py-3 text-gray-500">${mat.minimum_stock} ${mat.unit}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">${mat.unit}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">${mat.packagings_count} kemasan</td>
                    <td class="px-4 py-3">${statusBadge}</td>
                </tr>
            `;
        }).join("");
    }

    searchInput.addEventListener("input", renderMaterials);
    filterStatusSelect.addEventListener("change", renderMaterials);
    btnRefresh.addEventListener("click", loadStockReport);

    loadStockReport();
});
