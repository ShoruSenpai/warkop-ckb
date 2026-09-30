import { apiFetch } from "../utils/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const listEl = document.getElementById("transaction-list");
    const paginationEl = document.getElementById("pagination-container");

    const startDateInput = document.getElementById("filter-start-date");
    const endDateInput = document.getElementById("filter-end-date");
    const methodSelect = document.getElementById("filter-payment-method");
    const searchInput = document.getElementById("filter-search");
    const btnApply = document.getElementById("btn-apply-filter");
    const btnReset = document.getElementById("btn-reset-filter");

    const detailModal = document.getElementById("tx-detail-modal");
    const btnCloseModal = document.getElementById("btn-close-modal");

    let currentPage = 1;

    function formatRupiah(num) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(num || 0);
    }

    async function loadTransactions(page = 1) {
        currentPage = page;
        listEl.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat riwayat transaksi...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: page,
        });

        if (startDateInput.value) params.append("start_date", startDateInput.value);
        if (endDateInput.value) params.append("end_date", endDateInput.value);
        if (methodSelect.value) params.append("payment_method", methodSelect.value);
        if (searchInput.value.trim()) params.append("search", searchInput.value.trim());

        try {
            const { data } = await apiFetch(`/api/reports/transactions?${params.toString()}`);
            renderTable(data.data.data);
            renderPagination(data.data);
        } catch (err) {
            listEl.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-sm text-red-500">
                        Gagal memuat data transaksi.
                    </td>
                </tr>
            `;
        }
    }

    function renderTable(orders) {
        if (!orders || orders.length === 0) {
            listEl.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">
                        Tidak ada transaksi ditemukan.
                    </td>
                </tr>
            `;
            return;
        }

        listEl.innerHTML = orders.map(order => {
            const cashierName = order.cashier?.full_name || order.cashier?.username || 'Kasir';
            const methodBadge = order.payment_method === 'qris' 
                ? 'bg-blue-50 text-blue-600' 
                : (order.payment_method === 'transfer' ? 'bg-purple-50 text-purple-600' : 'bg-green-50 text-green-600');

            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition text-sm">
                    <td class="px-4 py-3 font-semibold text-ckb-primary">${order.invoice_code}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">${new Date(order.transaction_time).toLocaleString('id-ID')}</td>
                    <td class="px-4 py-3 text-gray-700">${cashierName}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase ${methodBadge}">
                            ${order.payment_method}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">${formatRupiah(order.gross_amount)}</td>
                    <td class="px-4 py-3 text-red-500">${formatRupiah(order.discount_amount)}</td>
                    <td class="px-4 py-3 font-bold text-gray-900">${formatRupiah(order.final_amount)}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" data-order='${JSON.stringify(order).replace(/'/g, "&apos;")}' class="btn-detail px-3 py-1 bg-ckb-primary/10 text-ckb-primary hover:bg-ckb-primary hover:text-white rounded text-xs font-semibold transition">
                            <i class="fas fa-eye mr-1"></i> Rincian
                        </button>
                    </td>
                </tr>
            `;
        }).join("");

        document.querySelectorAll(".btn-detail").forEach(btn => {
            btn.addEventListener("click", () => {
                const orderData = JSON.parse(btn.getAttribute("data-order"));
                showDetailModal(orderData);
            });
        });
    }

    function renderPagination(meta) {
        if (!meta || meta.total <= meta.per_page) {
            paginationEl.innerHTML = `<span>Menampilkan ${meta.total || 0} transaksi</span>`;
            return;
        }

        paginationEl.innerHTML = `
            <span>Menampilkan ${meta.from || 0} - ${meta.to || 0} dari ${meta.total} transaksi</span>
            <div class="flex gap-1">
                <button ${meta.current_page === 1 ? 'disabled' : ''} id="btn-prev-page" class="px-3 py-1 rounded border border-gray-200 text-xs disabled:opacity-50">Sebelumnnya</button>
                <button ${meta.current_page === meta.last_page ? 'disabled' : ''} id="btn-next-page" class="px-3 py-1 rounded border border-gray-200 text-xs disabled:opacity-50">Selanjutnya</button>
            </div>
        `;

        const btnPrev = document.getElementById("btn-prev-page");
        const btnNext = document.getElementById("btn-next-page");

        if (btnPrev && !btnPrev.disabled) {
            btnPrev.addEventListener("click", () => loadTransactions(meta.current_page - 1));
        }
        if (btnNext && !btnNext.disabled) {
            btnNext.addEventListener("click", () => loadTransactions(meta.current_page + 1));
        }
    }

    function showDetailModal(order) {
        document.getElementById("modal-invoice-code").textContent = order.invoice_code;
        document.getElementById("modal-tx-time").textContent = new Date(order.transaction_time).toLocaleString('id-ID');
        document.getElementById("modal-cashier-name").textContent = order.cashier?.full_name || 'Kasir';
        document.getElementById("modal-payment-method").textContent = order.payment_method;
        document.getElementById("modal-payment-status").textContent = order.payment_status || 'PAID';
        document.getElementById("modal-gross-amount").textContent = formatRupiah(order.gross_amount);
        document.getElementById("modal-discount-amount").textContent = formatRupiah(order.discount_amount);
        document.getElementById("modal-final-amount").textContent = formatRupiah(order.final_amount);

        const itemsContainer = document.getElementById("modal-items-list");
        if (order.order_items && order.order_items.length > 0) {
            itemsContainer.innerHTML = order.order_items.map(item => `
                <li class="py-2 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-semibold text-gray-800">${item.product?.name || 'Produk'}</p>
                        <p class="text-gray-400">${item.quantity} x ${formatRupiah(item.unit_price)}</p>
                    </div>
                    <span class="font-bold text-gray-700">${formatRupiah(item.subtotal)}</span>
                </li>
            `).join("");
        } else {
            itemsContainer.innerHTML = `<li class="py-2 text-xs text-gray-400">Tidak ada rincian item.</li>`;
        }

        detailModal.classList.remove("hidden");
        detailModal.classList.add("flex");
    }

    if (btnCloseModal) {
        btnCloseModal.addEventListener("click", () => {
            detailModal.classList.add("hidden");
            detailModal.classList.remove("flex");
        });
    }

    btnApply.addEventListener("click", () => loadTransactions(1));
    btnReset.addEventListener("click", () => {
        startDateInput.value = "";
        endDateInput.value = "";
        methodSelect.value = "";
        searchInput.value = "";
        loadTransactions(1);
    });

    loadTransactions(1);
});
