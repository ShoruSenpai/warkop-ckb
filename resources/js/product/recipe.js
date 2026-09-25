import { state } from "./state.js";
import { apiFetch } from "../utils/api.js";
import { $, escapeHtml } from "./helper.js";

export async function fetchRawMaterials() {
    try {
        const { response, data } = await apiFetch("/api/raw-materials");

        if (!response.ok) {
            console.error("Gagal mengambil raw materials:", data);

            return;
        }

        state.rawMaterials = Array.isArray(data.data) ? data.data : [];
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error("Gagal mengambil raw materials:", error);
    }
}

export async function prepareRecipeBuilder() {
    if (!state.rawMaterials.length) {
        await fetchRawMaterials();
    }

    renderRecipeRows();
}

export function setRecipeRows(recipes) {
    state.recipeRows = Array.isArray(recipes)
        ? recipes.map((recipe) => ({
              raw_material_id: recipe.raw_material_id,

              amount_needed: recipe.amount_needed,
          }))
        : [];

    renderRecipeRows();
}

export function getRecipeRows() {
    return state.recipeRows;
}

export function addRecipeRow() {
    state.recipeRows.push({
        raw_material_id: "",
        amount_needed: "",
    });

    renderRecipeRows();
}

export function removeRecipeRow(index) {
    state.recipeRows.splice(index, 1);

    renderRecipeRows();
}

export function updateRecipeMaterial(index, value) {
    if (!state.recipeRows[index]) {
        return;
    }

    state.recipeRows[index].raw_material_id = value;

    renderRecipeRows();
}

export function updateRecipeAmount(index, value) {
    if (!state.recipeRows[index]) {
        return;
    }

    state.recipeRows[index].amount_needed = value;
}

export function renderRecipeRows() {
    const list = $("recipe-list");

    const empty = $("recipe-empty");

    if (!list || !empty) {
        return;
    }

    empty.classList.toggle("hidden", state.recipeRows.length > 0);

    if (!state.recipeRows.length) {
        list.innerHTML = "";
        return;
    }

    list.innerHTML = state.recipeRows
        .map((row, index) => {
            const material = state.rawMaterials.find(
                (item) => String(item.id) === String(row.raw_material_id),
            );

            return `
                        <div
                            class="
                                grid
                                grid-cols-1
                                gap-3
                                rounded-xl
                                border
                                border-ckb-outline-variant/30
                                bg-ckb-surface-container-low
                                p-3
                                md:grid-cols-[1fr_180px_auto]
                                md:items-end
                            "
                        >

                            <div>
                                <label
                                    class="
                                        mb-1 block
                                        text-[11px]
                                        font-semibold
                                        text-ckb-primary
                                    "
                                >
                                    Bahan Baku
                                </label>

                                <select
                                    class="
                                        recipe-material
                                        w-full rounded-lg
                                        border border-ckb-outline-variant/50
                                        bg-ckb-surface-container-lowest
                                        px-3 py-2
                                        text-sm
                                        text-ckb-on-surface
                                        outline-none
                                        focus:border-ckb-primary
                                    "
                                    data-index="${index}"
                                >
                                    <option value="">
                                        -- Pilih Bahan --
                                    </option>

                                    ${state.rawMaterials
                                        .map(
                                            (material) => `
                                                <option
                                                    value="${Number(
                                                        material.id,
                                                    )}"
                                                    ${
                                                        String(material.id) ===
                                                        String(
                                                            row.raw_material_id,
                                                        )
                                                            ? "selected"
                                                            : ""
                                                    }
                                                >
                                                    ${escapeHtml(material.name)}
                                                    (${escapeHtml(
                                                        material.unit_measurement,
                                                    )})
                                                </option>
                                            `,
                                        )
                                        .join("")}
                                </select>
                            </div>


                            <div>
                                <label
                                    class="
                                        mb-1 block
                                        text-[11px]
                                        font-semibold
                                        text-ckb-primary
                                    "
                                >
                                    Kebutuhan / Produk
                                </label>

                                <div class="relative">

                                    <input
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        value="${row.amount_needed ?? ""}"
                                        class="
                                            recipe-amount
                                            w-full rounded-lg
                                            border border-ckb-outline-variant/50
                                            bg-ckb-surface-container-lowest
                                            px-3 py-2
                                            pr-16
                                            text-sm
                                            text-ckb-on-surface
                                            outline-none
                                            focus:border-ckb-primary
                                        "
                                        data-index="${index}"
                                    >

                                    <span
                                        class="
                                            pointer-events-none
                                            absolute right-3 top-1/2
                                            -translate-y-1/2
                                            text-xs text-ckb-outline
                                        "
                                    >
                                        ${escapeHtml(
                                            material?.unit_measurement ??
                                                "unit",
                                        )}
                                    </span>

                                </div>
                            </div>


                            <button
                                type="button"
                                data-action="remove-recipe"
                                data-index="${index}"
                                title="Hapus bahan"
                                class="
                                    flex h-10 w-10
                                    items-center justify-center
                                    rounded-lg
                                    text-ckb-error
                                    hover:bg-ckb-error-container
                                "
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>
                    `;
        })
        .join("");
}

export function initRecipeEvents() {
    $("btn-add-recipe")?.addEventListener("click", addRecipeRow);

    $("recipe-list")?.addEventListener("change", (event) => {
        if (event.target.matches(".recipe-material")) {
            updateRecipeMaterial(
                Number(event.target.dataset.index),
                event.target.value,
            );
        }
    });

    $("recipe-list")?.addEventListener("input", (event) => {
        if (event.target.matches(".recipe-amount")) {
            updateRecipeAmount(
                Number(event.target.dataset.index),
                event.target.value,
            );
        }
    });

    $("recipe-list")?.addEventListener("click", (event) => {
        const button = event.target.closest('[data-action="remove-recipe"]');

        if (!button) return;

        removeRecipeRow(Number(button.dataset.index));
    });
}
