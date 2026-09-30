import { apiFetch } from "@/utils/api.js";

let purchaseReports = [];

document.addEventListener("DOMContentLoaded", () => {
    fetchPurchaseReports();
});

async function fetchPurchaseReports() {
    try {
        const { response, data } = await apiFetch("/api/reports/purchases");

        if (!response.ok) {
            renderPurchaseReportsError(
                data?.message || "Laporan pembelian tidak dapat dimuat.",
            );

            return;
        }

        purchaseReports = Array.isArray(data.data) ? data.data : [];

        renderPurchaseReports(purchaseReports);
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error("Gagal mengambil laporan pembelian:", error);

        renderPurchaseReportsError("Laporan pembelian tidak dapat dimuat.");
    }
}

function renderPurchaseReports(data) {
    const tbody = document.getElementById("purchase-report-list");

    const summary = document.getElementById("summary-text");

    if (!tbody) return;

    if (summary) {
        summary.innerText = `${data.length} transaksi pembelian`;
    }

    if (!data.length) {
        tbody.innerHTML = `
            <tr>
                <td
                    colspan="4"
                    class="
                        px-4 py-10
                        text-center
                        text-sm
                        text-ckb-outline
                    "
                >
                    Belum ada riwayat pembelian.
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = data
        .map((purchase) => {
            const status = getStatusConfig(purchase.status);

            return `
                <tr
                    class="
                        border-b
                        border-ckb-outline-variant/20
                        text-sm
                        transition-colors
                        hover:bg-ckb-surface-container-low
                    "
                >

                    <td class="px-4 py-4">
                        <span
                            class="
                                font-semibold
                                text-ckb-on-surface
                            "
                        >
                            ${escapeHtml(purchase.supplier_name ?? "-")}
                        </span>
                    </td>

                    <td
                        class="
                            px-4 py-4
                            text-ckb-on-surface-variant
                        "
                    >
                        ${formatDate(purchase.purchase_date)}
                    </td>

                    <td class="px-4 py-4">
                        <span
                            class="
                                rounded-md
                                bg-ckb-surface-container
                                px-2 py-1
                                font-mono
                                text-xs
                                font-semibold
                                text-ckb-primary
                            "
                        >
                            ${escapeHtml(purchase.invoice_number ?? "-")}
                        </span>
                    </td>

                    <td class="px-4 py-4">
                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                px-2.5 py-1
                                text-xs
                                font-semibold
                                ${status.classes}
                            "
                        >
                            <i
                                class="
                                    fas
                                    ${status.icon}
                                "
                            ></i>

                            ${escapeHtml(status.label)}
                        </span>
                    </td>

                </tr>
            `;
        })
        .join("");
}

function getStatusConfig(status) {
    const configs = {
        received: {
            label: "Diterima",
            icon: "fa-check-circle",
            classes:
                "bg-ckb-secondary-container/20 text-ckb-on-secondary-container",
        },

        pending: {
            label: "Pending",
            icon: "fa-clock",
            classes:
                "bg-ckb-tertiary-container/20 text-ckb-on-tertiary-container",
        },
    };

    return (
        configs[status] || {
            label: status || "Tidak diketahui",
            icon: "fa-circle",
            classes: "bg-ckb-surface-container text-ckb-on-surface-variant",
        }
    );
}

function formatDate(dateString) {
    if (!dateString) return "-";

    const date = new Date(`${dateString}T00:00:00`);

    if (Number.isNaN(date.getTime())) {
        return "-";
    }

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

function renderPurchaseReportsError(message) {
    const tbody = document.getElementById("purchase-report-list");

    const summary = document.getElementById("summary-text");

    if (summary) {
        summary.innerText = "Gagal memuat data";
    }

    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td
                colspan="4"
                class="
                    px-4 py-10
                    text-center
                    text-sm
                    text-ckb-error
                "
            >
                ${escapeHtml(message)}
            </td>
        </tr>
    `;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}
