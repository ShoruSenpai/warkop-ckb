import { state } from "./state.js";
import { apiFetch } from "../utils/api.js";
import { $, escapeHtml, showApiError } from "./helper.js";

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
                    colspan="6"
                    class="py-8 text-center text-gray-400"
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

            const isCritical = stock <= 100;

            if (isCritical) {
                criticalCount++;
            }

            const status = isCritical
                ? `
                    <span class="text-red-700 bg-red-50 px-2 py-1 rounded text-xs font-semibold">
                        ● Menipis
                    </span>
                `
                : `
                    <span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs font-semibold">
                        ● Aman
                    </span>
                `;

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

                    <td class="py-3 font-bold text-ckb-secondary">
                        ${stock.toLocaleString("id-ID")}
                        ${escapeHtml(item.unit_measurement)}
                    </td>

                    <td class="py-3">
                        ${status}
                    </td>

                    <td class="py-3">
                        <div class="flex justify-end gap-2">

                            <button
                                type="button"
                                data-action="edit"
                                data-id="${Number(item.id)}"
                                class="px-2.5 py-1.5 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-semibold"
                                title="Edit"
                            >
                                <i class="fas fa-edit"></i>
                            </button>

                            <button
                                type="button"
                                data-action="delete"
                                data-id="${Number(item.id)}"
                                class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold"
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
