import { apiFetch } from "../utils/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const elTodayOmzet = document.getElementById("dash-today-omzet");
    const elOmzetDiff = document.getElementById("dash-omzet-diff");
    const elTodayTx = document.getElementById("dash-today-tx");
    const elTxDiff = document.getElementById("dash-tx-diff");
    const elActiveProducts = document.getElementById("dash-active-products");
    const elTotalProducts = document.getElementById("dash-total-products");
    const elCriticalStock = document.getElementById("dash-critical-stock");
    const chartContainer = document.getElementById("chart-container");
    const recentTxList = document.getElementById("recent-tx-list");

    function formatRupiah(num) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(num || 0);
    }

    async function loadDashboard() {
        try {
            const { data } = await apiFetch('/api/dashboard/summary');
            const res = data.data;

            elTodayOmzet.textContent = formatRupiah(res.today_omzet);
            
            if (res.omzet_diff_percent >= 0) {
                elOmzetDiff.textContent = `+${res.omzet_diff_percent}% vs kemarin`;
                elOmzetDiff.className = "text-[10px] text-green-600 font-medium mt-1";
            } else {
                elOmzetDiff.textContent = `${res.omzet_diff_percent}% vs kemarin`;
                elOmzetDiff.className = "text-[10px] text-red-500 font-medium mt-1";
            }

            elTodayTx.textContent = res.today_tx_count;
            if (res.tx_diff_count >= 0) {
                elTxDiff.textContent = `+${res.tx_diff_count} dari kemarin`;
                elTxDiff.className = "text-[10px] text-green-600 font-medium mt-1";
            } else {
                elTxDiff.textContent = `${res.tx_diff_count} dari kemarin`;
                elTxDiff.className = "text-[10px] text-red-500 font-medium mt-1";
            }

            elActiveProducts.textContent = res.active_products_count;
            elTotalProducts.textContent = `dari ${res.total_products_count} total`;

            elCriticalStock.textContent = res.critical_stock_count;

            renderChart(res.chart_data);
            renderRecentTransactions(res.recent_transactions);
        } catch (err) {
            console.error("Gagal memuat data dashboard:", err);
        }
    }

    function renderChart(chartData) {
        if (!chartData || chartData.length === 0) {
            chartContainer.innerHTML = `<div class="w-full text-center text-gray-400 text-sm py-20">Belum ada data grafik.</div>`;
            return;
        }

        const maxOmzet = Math.max(...chartData.map(d => d.omzet), 100000);

        chartContainer.innerHTML = chartData.map(d => {
            const heightPercent = Math.max(10, Math.round((d.omzet / maxOmzet) * 100));
            return `
                <div class="flex-1 flex flex-col items-center h-full justify-end group">
                    <div class="text-[10px] font-bold text-ckb-primary opacity-0 group-hover:opacity-100 transition mb-1">
                        ${formatRupiah(d.omzet)}
                    </div>
                    <div class="w-full max-w-[32px] bg-ckb-primary/20 group-hover:bg-ckb-primary rounded-t-md transition-all" style="height: ${heightPercent}%;"></div>
                    <span class="text-[10px] text-gray-500 mt-2 font-medium truncate w-full text-center">${d.label}</span>
                </div>
            `;
        }).join("");
    }

    function renderRecentTransactions(transactions) {
        if (!transactions || transactions.length === 0) {
            recentTxList.innerHTML = `<li class="py-4 text-center text-xs text-gray-400">Belum ada transaksi hari ini.</li>`;
            return;
        }

        recentTxList.innerHTML = transactions.map(tx => `
            <li class="py-2 flex justify-between items-center text-xs">
                <div>
                    <p class="font-bold text-gray-800">${tx.invoice_code}</p>
                    <p class="text-[10px] text-gray-400">${new Date(tx.transaction_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} • ${tx.payment_method.toUpperCase()}</p>
                </div>
                <span class="font-bold text-ckb-primary">${formatRupiah(tx.final_amount)}</span>
            </li>
        `).join("");
    }

    loadDashboard();
});
