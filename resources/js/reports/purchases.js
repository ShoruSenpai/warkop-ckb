import { apiFetch } from "../utils/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const startDateInput = document.getElementById("purchase-start-date");
    const endDateInput = document.getElementById("purchase-end-date");
    const btnFilter = document.getElementById("btn-refresh-purchase");

    const cardSpent = document.getElementById("card-total-spent");
    const cardInvoices = document.getElementById("card-total-invoices");
    const listEl = document.getElementById("purchases-list");

    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    startDateInput.value = firstDay.toISOString().split("T")[0];
    endDateInput.value = today.toISOString().split("T")[0];

    function formatRupiah(num) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(num || 0);
    }

    async function loadPurchaseReport() {
        listEl.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat laporan pembelian...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            start_date: startDateInput.value,
            end_date: endDateInput.value,
        });

        try {
            const { data } = await apiFetch(`/api/reports/purchases?${params.toString()}`);
            const summary = data.data.summary;
            const purchases = data.data.purchases;

            cardSpent.textContent = formatRupiah(summary.total_spent);
            cardInvoices.textContent = `${summary.total_invoices} Nota`;

            renderPurchases(purchases);
        } catch (err) {
            listEl.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-red-500">
                        Gagal memuat data laporan pembelian.
                    </td>
                </tr>
            `;
        }
    }

    function renderPurchases(purchases) {
        if (!purchases || purchases.length === 0) {
            listEl.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                        Belum ada riwayat pembelian supplier pada periode ini.
                    </td>
                </tr>
            `;
            return;
        }

        listEl.innerHTML = purchases.map(item => {
            const itemsText = item.items ? item.items.map(i => `${i.raw_material?.name || 'Bahan'} (${i.purchase_qty} ${i.raw_material_packaging?.purchase_unit || 'satuan'})`).join(', ') : '-';
            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition text-sm">
                    <td class="px-4 py-3 font-semibold text-ckb-primary">${item.invoice_number}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">${new Date(item.purchase_date).toLocaleDateString('id-ID')}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">${item.supplier_name || 'Supplier'}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-xs truncate" title="${itemsText}">${itemsText}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">${formatRupiah(item.total_cost)}</td>
                </tr>
            `;
        }).join("");
    }

    btnFilter.addEventListener("click", loadPurchaseReport);
    loadPurchaseReport();
});
