import { state } from "./state.js";
import { apiFetch } from "../utils/api.js";
import { $, escapeHtml, showApiError, toggleModal } from "./helper.js";

export function setProductPackagings(packagings) {
    state.productPackagings = Array.isArray(packagings) ? packagings : [];

    renderPackagingList();
}

export function renderPackagingList() {
    const list = $("packaging-list");

    const empty = $("packaging-empty");

    if (!list || !empty) {
        return;
    }

    empty.classList.toggle("hidden", state.productPackagings.length > 0);

    list.innerHTML = state.productPackagings
        .map(
            (packaging) => `
                    <div
                        class="
                            flex items-center justify-between
                            gap-4 rounded-lg
                            border border-ckb-outline-variant/30
                            bg-ckb-surface-container-low
                            px-4 py-3
                        "
                    >

                        <div>
                            <p class="
                                text-sm font-semibold
                                text-ckb-primary
                            ">
                                ${escapeHtml(packaging.purchase_unit)}
                            </p>

                            <p class="
                                mt-0.5
                                text-xs
                                text-ckb-on-surface-variant
                            ">
                                1
                                ${escapeHtml(packaging.purchase_unit)}
                                =
                                ${Number(
                                    packaging.conversion_factor,
                                ).toLocaleString("id-ID")}
                                ${escapeHtml(
                                    state.currentProduct?.base_unit ?? "",
                                )}
                            </p>
                        </div>


                        <div class="flex items-center gap-1">

                            <button
                                type="button"
                                data-action="edit-packaging"
                                data-id="${Number(packaging.id)}"
                                class="
                                    flex h-8 w-8
                                    items-center justify-center
                                    rounded-lg
                                    text-ckb-primary
                                    hover:bg-ckb-primary-container
                                "
                                title="Edit"
                            >
                                <i class="fas fa-pen text-xs"></i>
                            </button>

                            <button
                                type="button"
                                data-action="delete-packaging"
                                data-id="${Number(packaging.id)}"
                                class="
                                    flex h-8 w-8
                                    items-center justify-center
                                    rounded-lg
                                    text-ckb-error
                                    hover:bg-ckb-error-container
                                "
                                title="Hapus"
                            >
                                <i class="fas fa-trash text-xs"></i>
                            </button>

                        </div>

                    </div>
                `,
        )
        .join("");
}

export function openPackagingModal(packaging = null) {
    state.editingPackagingId = packaging?.id ?? null;

    $("packaging-modal-title").innerText = packaging
        ? "Edit Satuan"
        : "Tambah Satuan";

    $("packaging-unit").value = packaging?.purchase_unit ?? "";

    $("packaging-conversion").value = packaging?.conversion_factor ?? "";

    toggleModal("packaging-modal", true);
}

export function closePackagingModal() {
    toggleModal("packaging-modal", false);

    state.editingPackagingId = null;
}

export async function savePackaging() {
    if (!state.productId) {
        await Swal.fire({
            icon: "info",
            title: "Simpan produk dulu",
            text: "Packaging baru dapat dikelola setelah produk tersimpan.",
        });

        return;
    }

    const purchaseUnit = $("packaging-unit").value.trim();

    const conversionFactor = Number($("packaging-conversion").value);

    if (
        !purchaseUnit ||
        !Number.isFinite(conversionFactor) ||
        conversionFactor <= 0
    ) {
        await Swal.fire({
            icon: "warning",
            title: "Data belum lengkap",
            text: "Isi satuan pembelian dan konversinya.",
        });

        return;
    }

    const id = state.editingPackagingId;

    const url = id
        ? `/api/products/${state.productId}/packagings/${id}`
        : `/api/products/${state.productId}/packagings`;

    const method = id ? "PUT" : "POST";

    try {
        const { response, data } = await apiFetch(url, {
            method,
            body: {
                purchase_unit: purchaseUnit,

                conversion_factor: conversionFactor,

                is_active: true,
            },
        });

        if (!response.ok) {
            await showApiError(
                data,
                id ? "Gagal Mengubah Satuan" : "Gagal Menambahkan Satuan",
            );

            return;
        }

        closePackagingModal();

        await reloadProductPackagings();

        await Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: data?.message || "Satuan berhasil disimpan.",
            timer: 1300,
            showConfirmButton: false,
        });
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: error.message || "Packaging gagal disimpan.",
        });
    }
}

export async function reloadProductPackagings() {
    if (!state.productId) return;

    const { response, data } = await apiFetch(
        `/api/products/${state.productId}/packagings`,
    );

    if (!response.ok) {
        await showApiError(data, "Gagal Memuat Satuan");

        return;
    }

    setProductPackagings(data.data);
}

export async function deletePackaging(id) {
    const packaging = state.productPackagings.find(
        (item) => Number(item.id) === Number(id),
    );

    if (!packaging) return;

    const confirmed = await Swal.fire({
        icon: "warning",
        title: "Hapus satuan?",
        text: "Satuan yang sudah digunakan dalam pembelian tidak dapat dihapus.",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        confirmButtonColor: "#ba1a1a",
    });

    if (!confirmed.isConfirmed) {
        return;
    }

    try {
        const { response, data } = await apiFetch(
            `/api/products/${state.productId}/packagings/${id}`,
            {
                method: "DELETE",
            },
        );

        if (!response.ok) {
            await showApiError(data, "Gagal Menghapus Satuan");

            return;
        }

        await reloadProductPackagings();

        await Swal.fire({
            icon: "success",
            title: "Satuan dihapus",
            timer: 1300,
            showConfirmButton: false,
        });
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: error.message || "Satuan tidak dapat dihapus.",
        });
    }
}

export function initPackagingEvents() {
    $("btn-add-packaging")?.addEventListener("click", () =>
        openPackagingModal(),
    );

    $("packaging-modal")?.addEventListener("click", (event) => {
        if (event.target.closest("[data-modal-close]")) {
            closePackagingModal();
            return;
        }

        const button = event.target.closest("[data-action]");

        if (!button) return;

        const id = Number(button.dataset.id);

        switch (button.dataset.action) {
            case "edit-packaging":
                const packaging = state.productPackagings.find(
                    (item) => Number(item.id) === id,
                );

                if (packaging) {
                    openPackagingModal(packaging);
                }

                break;

            case "delete-packaging":
                deletePackaging(id);
                break;
        }
    });

    $("packaging-form")?.addEventListener("submit", (event) => {
        event.preventDefault();
        savePackaging();
    });
}
