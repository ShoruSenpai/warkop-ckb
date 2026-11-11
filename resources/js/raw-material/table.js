import { state } from "./state.js";
import { apiFetch } from "../utils/api.js";
import { $, escapeHtml } from "@/utils/dom.js";
import { showApiError } from "@/utils/ui.js";

export async function fetchRawMaterials() {
    try {
        const { response, data } = await apiFetch("/api/raw-materials");

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Bahan Baku");

            return;
        }

        state.rawMaterialData = Array.isArray(data.data) ? data.data : [];

        renderTable(state.rawMaterialData);
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message:
                "Bahan baku tidak dapat dimuat. Periksa koneksi lalu coba lagi.",
        });
    }
}

export function renderTable(items) {
    const tbody = $("raw-material-list");
    const alertBox = $("critical-alert");
    const summary = $("summary-text");

    if (!items.length) {
        tbody.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="px-3 py-8 text-center text-xs text-ckb-on-surface-variant"
                >
                    Belum ada bahan baku terdaftar.
                </td>
            </tr>
        `;

        summary.innerText = "0 bahan terdaftar";

        alertBox.classList.add("hidden");

        return;
    }

    let criticalCount = 0;

    tbody.innerHTML = items
        .map((item) => {
            const stock = parseFloat(item.current_stock) || 0;
            const minimumStock = parseFloat(item.minimum_stock) || 0;

            let status;
            let isCritical = false;

            if (stock <= 0) {
                status = `
                    <span class="text-ckb-on-error-container bg-ckb-error-container px-2 py-1 rounded text-xs font-semibold">
                        ● Habis
                    </span>
                `;

                isCritical = true;
            } else if (stock <= minimumStock) {
                status = `
                    <span class="text-ckb-on-warning-container bg-ckb-warning-container px-2 py-1 rounded text-xs font-semibold">
                        ● Menipis
                    </span>
                `;

                isCritical = true;
            } else {
                status = `
                    <span class="text-ckb-on-success-container bg-ckb-success-container px-2 py-1 rounded text-xs font-semibold">
                        ● Aman
                    </span>
                `;
            }

            if (isCritical) {
                criticalCount++;
            }

            return `
                <tr class="border-b border-ckb-outline-variant/30 text-sm">

                    <td class="px-3 py-2.5 text-ckb-on-surface-variant text-[11px] font-mono">
                        #RM-${escapeHtml(item.id)}
                    </td>

                    <td class="px-3 py-2.5 text-xs text-ckb-on-surface font-medium">
                        ${escapeHtml(item.name)}
                    </td>

                    <td class="px-3 py-2.5 text-xs text-ckb-on-surface-variant">
                        <span class="bg-ckb-surface-container px-2 py-0.5 rounded text-xs text-ckb-on-surface">
                            ${escapeHtml(item.unit_measurement)}
                        </span>
                    </td>

                    <td class="px-3 py-2.5 text-xs font-bold text-ckb-primary">
                        ${stock.toLocaleString("id-ID")}
                        ${escapeHtml(item.unit_measurement)}
                    </td>

                    <td class="px-3 py-2.5 text-xs text-ckb-on-surface-variant font-semibold">
                            ${minimumStock.toLocaleString("id-ID")}
                            ${escapeHtml(item.unit_measurement)}
                    </td>

                    <td class="px-3 py-2.5">
                        ${status}
                    </td>

                    <td class="px-3 py-2.5">
                        <div class="flex justify-end gap-2">

                            <button
                                type="button"
                                data-action="edit"
                                data-id="${Number(item.id)}"
                                class="px-2.5 py-1.5 rounded-lg bg-ckb-surface-container-low text-ckb-on-surface hover:bg-ckb-surface-container text-xs font-semibold"
                                title="Edit"
                            >
                                <i class="fas fa-edit"></i>
                            </button>

                            <button
                                type="button"
                                data-action="delete"
                                data-id="${Number(item.id)}"
                                class="px-2.5 py-1.5 rounded-lg bg-ckb-error-container text-ckb-on-error-container hover:bg-ckb-error/15 text-xs font-semibold"
                                title="Hapus"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>
                    </td>

                </tr>
            `;
        })
        .join("");

    summary.innerText = `${items.length} bahan terdaftar · ${criticalCount} perlu perhatian`;

    alertBox.classList.toggle("hidden", criticalCount === 0);

    if (criticalCount) {
        $("alert-title").innerText =
            `${criticalCount} bahan baku dalam kondisi kritis!`;
    }
}

export function filterTable() {
    const query = $("search-input").value.trim().toLowerCase();

    renderTable(
        state.rawMaterialData.filter((item) =>
            item.name.toLowerCase().includes(query),
        ),
    );
}

export function initTableEvents({ onEdit, onDelete }) {
    $("btn-get-materials")?.addEventListener("click", fetchRawMaterials);

    $("search-input")?.addEventListener("input", filterTable);

    $("raw-material-list")?.addEventListener("click", (event) => {
        const button = event.target.closest("[data-action]");

        if (!button) return;

        const id = Number(button.dataset.id);

        switch (button.dataset.action) {
            case "edit":
                onEdit?.(id);
                break;

            case "delete":
                onDelete?.(id);
                break;
        }
    });
}
