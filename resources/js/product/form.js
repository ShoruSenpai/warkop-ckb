import { state } from "./state.js";
import { apiFetch } from "@/utils/api.js";
import { $, escapeHtml } from "@/utils/dom.js";
import { showApiError } from "@/utils/ui.js";

import {
    prepareRecipeBuilder,
    setRecipeRows,
    getRecipeRows,
    initRecipeEvents,
} from "./recipe.js";

import { setProductPackagings, initPackagingEvents } from "./packaging.js";

import { handleStockTypeChange, initStockTypeEvents } from "./stock-type.js";

function readFormConfig() {
    const wrapper = $("product-form-wrapper");

    if (!wrapper) return;

    state.productMode = wrapper.dataset.mode || "create";

    state.productId = wrapper.dataset.productId || null;
}

export async function fetchCategories() {
    try {
        const { response, data } = await apiFetch("/api/categories");

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Kategori");

            return;
        }

        state.categories = Array.isArray(data.data) ? data.data : [];

        const select = $("product-category");

        if (!select) return;

        select.innerHTML = `
            <option value="">
                -- Pilih Kategori --
            </option>

            ${state.categories
                .map(
                    (category) => `
                        <option
                            value="${Number(category.id)}"
                        >
                            ${escapeHtml(category.name)}
                        </option>
                    `,
                )
                .join("")}
        `;
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);
    }
}

export async function fetchProductDetail(id) {
    try {
        const { response, data } = await apiFetch(`/api/products/${id}`);

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Produk");

            return;
        }

        state.currentProduct = data.data ?? data;

        fillProductForm(state.currentProduct);
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: error.message || "Produk tidak dapat dimuat.",
        });
    }
}

function fillProductForm(product) {
    $("product-name").value = product.name ?? "";

    $("product-category").value = product.category_id ?? "";

    $("product-price").value = Number(product.base_price ?? 0);

    $("product-description").value = product.description ?? "";

    // $("product-image").value = product.image_url ?? "";

    $("product-recommended").checked = Boolean(product.is_recommended);

    const stockRadio = document.querySelector(
        `input[name="stock_type"][value="${product.stock_type}"]`,
    );

    if (stockRadio) {
        stockRadio.checked = true;
    }

    setRecipeRows(product.recipes);

    setProductPackagings(product.packagings);

    handleStockTypeChange();
}

function buildProductFormData() {
    const stockType = document.querySelector(
        'input[name="stock_type"]:checked',
    )?.value;

    if (!stockType) {
        throw new Error("Pilih jenis stok produk terlebih dahulu.");
    }

    const recipes = getRecipeRows();

    if (stockType === "recipe") {
        if (!recipes.length) {
            throw new Error("Tambahkan minimal satu bahan untuk resep produk.");
        }

        const materialIds = new Set();

        for (const row of recipes) {
            const materialId = Number(row.raw_material_id);
            const amountNeeded = Number(row.amount_needed);

            if (!Number.isInteger(materialId) || materialId <= 0) {
                throw new Error("Pilih bahan baku pada setiap baris resep.");
            }

            if (materialIds.has(materialId)) {
                throw new Error("Bahan baku dalam resep tidak boleh duplikat.");
            }

            if (!Number.isInteger(amountNeeded) || amountNeeded <= 0) {
                throw new Error("Jumlah kebutuhan setiap bahan harus bilangan bulat lebih dari 0.");
            }

            materialIds.add(materialId);
        }
    }

    const formData = new FormData();

    formData.append("category_id", $("product-category").value);
    formData.append("name", $("product-name").value.trim());
    formData.append("base_price", $("product-price").value);
    formData.append("stock_type", stockType);
    formData.append("description", $("product-description").value.trim());
    formData.append(
        "is_recommended",
        $("product-recommended").checked ? "1" : "0",
    );
    formData.append("status", state.currentProduct?.status || "available");

    if (
        state.productMode === "create" &&
        stockType === "static" &&
        state.productPackagings.length
    ) {
        state.productPackagings.forEach((packaging, index) => {
            formData.append(
                `packagings[${index}][purchase_unit]`,
                packaging.purchase_unit,
            );

            formData.append(
                `packagings[${index}][conversion_factor]`,
                packaging.conversion_factor,
            );

            formData.append(
                `packagings[${index}][is_active]`,
                packaging.is_active ? "1" : "0",
            );
        });
    }

    if (stockType === "recipe") {
        recipes.forEach((row, index) => {
            formData.append(
                `recipes[${index}][raw_material_id]`,
                row.raw_material_id,
            );

            formData.append(
                `recipes[${index}][amount_needed]`,
                row.amount_needed,
            );
        });
    }

    const imageFile = $("product-image")?.files?.[0];

    if (state.productMode === "create" && imageFile) {
        formData.append("image", imageFile);
    }

    return formData;
}

export async function submitProduct(event) {
    event.preventDefault();

    const button = $("btn-save-product");

    button.disabled = true;

    button.innerHTML = `
        <i class="fas fa-spinner fa-spin"></i>
        <span>Menyimpan...</span>
    `;

    try {
        const payload = buildProductFormData();

        const isEdit = state.productMode === "edit";

        const url = isEdit
            ? `/api/products/${state.productId}`
            : "/api/products";

        const method = "POST";

        if (isEdit) {
            payload.append("_method", "PUT");
        }

        const { response, data } = await apiFetch(url, {
            method,
            body: payload,
        });

        if (!response.ok) {
            await showApiError(
                data,
                isEdit ? "Gagal Memperbarui Produk" : "Gagal Membuat Produk",
            );

            return;
        }

        const indexUrl = $("product-form-wrapper")?.dataset.indexUrl;

        if (!indexUrl) {
            throw new Error("Alamat halaman daftar produk tidak tersedia.");
        }

        window.location.assign(indexUrl);
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: error.message || "Data produk tidak dapat disimpan.",
        });
    } finally {
        button.disabled = false;

        button.innerHTML = `
            <i class="fas fa-save"></i>
            <span>Simpan Produk</span>
        `;
    }
}

export async function initProductForm() {
    readFormConfig();

    initRecipeEvents();
    initPackagingEvents();
    initStockTypeEvents();

    await Promise.all([fetchCategories(), prepareRecipeBuilder()]);

    if (state.productMode === "edit" && state.productId) {
        await fetchProductDetail(state.productId);
    }

    handleStockTypeChange();

    $("product-form")?.addEventListener("submit", submitProduct);
}

function initImagePreview() {
    const input = $("product-image");
    const preview = $("product-image-preview");
    const previewImage = $("product-image-preview-img");

    if (!input || !preview || !previewImage) {
        return;
    }

    input.addEventListener("change", () => {
        const file = input.files?.[0];

        if (!file) {
            preview.classList.add("hidden");
            previewImage.src = "";

            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            input.value = "";
            preview.classList.add("hidden");
            previewImage.src = "";

            Swal.fire({
                icon: "warning",
                title: "Ukuran gambar terlalu besar",
                text: "Ukuran gambar maksimal 5 MB.",
            });

            return;
        }

        previewImage.src = URL.createObjectURL(file);
        preview.classList.remove("hidden");
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initProductForm();
    initImagePreview();
});
