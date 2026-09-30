import { apiFetch } from "../utils/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const startDateInput = document.getElementById("sales-start-date");
    const endDateInput = document.getElementById("sales-end-date");
    const btnFilter = document.getElementById("btn-refresh-sales");

    const cardGross = document.getElementById("card-gross-revenue");
    const cardDiscount = document.getElementById("card-discount-amount");
    const cardNet = document.getElementById("card-net-revenue");
    const cardAvg = document.getElementById("card-avg-order");
    const topProductsList = document.getElementById("top-products-list");

    // Set default dates (first day of month until today)
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

    async function loadSalesReport() {
        topProductsList.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat laporan penjualan...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            start_date: startDateInput.value,
            end_date: endDateInput.value,
        });

        try {
            const { data } = await apiFetch(`/api/reports/sales?${params.toString()}`);
            const summary = data.data.summary;
            const topProducts = data.data.top_products;

            cardGross.textContent = formatRupiah(summary.gross_revenue);
            cardDiscount.textContent = formatRupiah(summary.discount_amount);
            cardNet.textContent = formatRupiah(summary.net_revenue);
            cardAvg.textContent = formatRupiah(summary.avg_order_value);

            renderTopProducts(topProducts);
        } catch (err) {
            topProductsList.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-red-500">
                        Gagal memuat data laporan penjualan.
                    </td>
                </tr>
            `;
        }
    }

    function renderTopProducts(products) {
        if (!products || products.length === 0) {
            topProductsList.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                        Belum ada penjualan produk pada periode ini.
                    </td>
                </tr>
            `;
            return;
        }

        topProductsList.innerHTML = products.map((item, index) => {
            const rankBadge = index === 0 ? 'bg-amber-100 text-amber-700 font-bold' : (index < 3 ? 'bg-gray-100 text-gray-700 font-bold' : 'text-gray-500');
            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition text-sm">
                    <td class="px-4 py-3">
                        <span class="inline-block w-6 h-6 leading-6 text-center rounded-full text-xs ${rankBadge}">
                            #${index + 1}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800">${item.product?.name || 'Produk'}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">${item.product?.category?.name || '-'}</td>
                    <td class="px-4 py-3 font-bold text-ckb-primary">${item.total_qty} porsi</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900">${formatRupiah(item.total_revenue)}</td>
                </tr>
            `;
        }).join("");
    }

    btnFilter.addEventListener("click", loadSalesReport);
    loadSalesReport();
});
