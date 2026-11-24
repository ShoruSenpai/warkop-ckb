import { state } from "./state.js";
import { apiFetch } from "@/utils/api.js";
import { $, escapeHtml } from "@/utils/dom.js";
import Swal from "@/utils/alert.js";
import { showApiError } from "@/utils/ui.js";
import { ckbColor } from "@/utils/theme.js";
import { runRefreshAction } from "@/utils/refresh.js";

const pendingProductDeletes = new Set();
const initializedProductLists = new WeakSet();

export async function fetchProducts() {
    try {
        const { response, data } = await apiFetch("/api/products");

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Produk");
            return false;
        }

        state.products = Array.isArray(data.data) ? data.data : [];

        applyFilters();
        return true;
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return false;
        }

        console.error("Gagal mengambil products:", error);

        const list = $("product-list");

        if (list) {
            list.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="px-3 py-8 text-center text-xs text-ckb-error"
                    >
                        Gagal memuat data produk.
                    </td>
                </tr>
            `;
        }

        return false;
    }
}

export async function fetchCategories() {
    try {
        const { response, data } = await apiFetch("/api/categories");

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Kategori");
            return;
        }

        state.categories = Array.isArray(data.data) ? data.data : [];

        renderCategoryFilter();
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error("Gagal mengambil kategori:", error);
    }
}

function renderCategoryFilter() {
    const select = $("category-filter");

    if (!select) return;

    select.innerHTML = `
        <option value="">
            Semua Kategori
        </option>

        ${state.categories
            .map(
                (category) => `
                    <option value="${category.id}">
                        ${escapeHtml(category.name)}
                    </option>
                `,
            )
            .join("")}
    `;
}

export function applyFilters() {
    const search = $("search-input")?.value.toLowerCase().trim() ?? "";

    const category = $("category-filter")?.value ?? "";

    const status = $("status-filter")?.value ?? "";

    const filtered = state.products.filter((product) => {
        const matchesSearch = product.name.toLowerCase().includes(search);

        const matchesCategory =
            !category || String(product.category_id) === String(category);

        const matchesStatus = !status || product.status === status;

        return matchesSearch && matchesCategory && matchesStatus;
    });

    renderProducts(filtered);
}

export function renderProducts(products) {
    const tbody = $("product-list");

    const summary = $("summary-text");

    if (!tbody) return;

    summary.innerText = `${state.products.length} produk terdaftar`;

    if (!products.length) {
        tbody.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="px-3 py-8 text-center text-xs text-ckb-on-surface-variant"
                >
                    Belum ada produk.
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = products
        .map((product) => {
            const stock =
                product.stock === null
                    ? "—"
                    : Number(product.stock).toLocaleString("id-ID");

            const stockTypeLabel =
                {
                    static: "Static",
                    recipe: "Recipe",
                    untracked: "Untracked",
                }[product.stock_type] ?? product.stock_type;

            const statusLabel =
                {
                    available: "Tersedia",
                    sold_out: "Habis",
                    disabled: "Nonaktif",
                }[product.status] ?? product.status;

            const statusClass =
                product.status === "available"
                    ? "bg-ckb-success-container text-ckb-on-success-container"
                    : product.status === "sold_out"
                      ? "bg-ckb-error-container text-ckb-on-error-container"
                      : "bg-ckb-surface-container text-ckb-on-surface-variant";

            return `
                    <tr class="
                        border-b
                        border-ckb-outline-variant/20
                        text-xs
                        last:border-0
                    ">

                        <td class="px-3 py-2.5">

                            <div class="flex items-center gap-3">

                                <div class="
                                    flex h-10 w-10 shrink-0
                                    items-center justify-center
                                    overflow-hidden
                                    rounded-lg
                                    bg-ckb-primary-container
                                ">
                                    ${
                                        product.image_url
                                            ? `
                                                <img
                                                    src="${escapeHtml(
                                                        product.image_url,
                                                    )}"
                                                    alt="${escapeHtml(
                                                        product.name,
                                                    )}"
                                                    class="h-full w-full object-cover"
                                                >
                                            `
                                            : `
                                                <i class="
                                                    fas fa-utensils
                                                    text-sm
                                                    text-ckb-primary
                                                "></i>
                                            `
                                    }
                                </div>

                                <div>
                                    <p class="
                                        font-semibold
                                        text-ckb-on-surface
                                    ">
                                        ${escapeHtml(product.name)}
                                    </p>

                                    ${
                                        product.is_recommended
                                            ? `
                                                <span class="
                                                    mt-1 inline-flex
                                                    items-center gap-1
                                                    text-xs font-semibold
                                                    text-ckb-accent
                                                ">
                                                    <i class="fas fa-star"></i>
                                                    Rekomendasi
                                                </span>
                                            `
                                            : ""
                                    }
                                </div>

                            </div>

                        </td>

                        <td class="
                            px-3 py-2.5
                            text-ckb-on-surface-variant
                        ">
                            ${
                                product.category?.name
                                    ? escapeHtml(product.category.name)
                                    : "—"
                            }
                        </td>

                        <td class="
                            px-3 py-2.5
                            font-semibold
                            text-ckb-primary
                        ">
                            Rp ${Number(product.base_price).toLocaleString(
                                "id-ID",
                            )}
                        </td>

                        <td class="px-3 py-2.5">

                            <span class="
                                inline-flex items-center
                                rounded-full
                                bg-ckb-surface-container
                                px-2.5 py-1
                                text-xs font-semibold
                                text-ckb-on-surface-variant
                            ">
                                ${stockTypeLabel}
                            </span>

                        </td>

                        <td class="
                            px-3 py-2.5
                            font-semibold
                            text-ckb-primary
                        ">
                            ${stock}
                        </td>

                        <td class="px-3 py-2.5">

                            <span class="
                                inline-flex items-center
                                rounded-full
                                px-2.5 py-1
                                text-xs font-semibold
                                ${statusClass}
                            ">
                                ${statusLabel}
                            </span>

                        </td>

                        <td class="px-3 py-2.5">

                            <div class="flex justify-end gap-2">

                                <a
                                    href="/products/${Number(product.id)}/edit"
                                    title="Edit Produk"
                                    aria-label="Edit ${escapeHtml(product.name)}"
                                    class="
                                        flex min-h-11 min-w-11 cursor-pointer
                                        items-center justify-center
                                        rounded-lg
                                        text-ckb-primary
                                        transition hover:bg-ckb-primary-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none
                                    "
                                >
                                    <i class="fas fa-pen"></i>
                                </a>

                                <button
                                    type="button"
                                    data-action="delete"
                                    data-id="${Number(product.id)}"
                                    title="Hapus Produk"
                                    aria-label="Hapus ${escapeHtml(product.name)}"
                                    class="
                                        flex min-h-11 min-w-11 cursor-pointer
                                        items-center justify-center
                                        rounded-lg
                                        text-ckb-error
                                        transition hover:bg-ckb-error-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none
                                    "
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </td>

                    </tr>
                `;
        })
        .join("");
}

export async function deleteProduct(id) {
    const productId = Number(id);

    if (!Number.isSafeInteger(productId) || productId <= 0) {
        await Swal.fire({
            icon: "error",
            title: "Produk tidak valid",
            text: "ID produk tidak tersedia. Muat ulang daftar produk lalu coba lagi.",
            confirmButtonText: "Mengerti",
        });
        return;
    }

    if (pendingProductDeletes.has(productId)) return;
    pendingProductDeletes.add(productId);

    const product = state.products.find(
        (item) => Number(item.id) === productId,
    );
    const deleteButton = $("product-list")?.querySelector(
        `button[data-action="delete"][data-id="${productId}"]`,
    );
    const deleteIcon = deleteButton?.querySelector("i");

    if (deleteButton) {
        deleteButton.disabled = true;
        deleteButton.setAttribute("aria-busy", "true");
        deleteIcon?.classList.add("fa-spinner", "fa-spin");
        deleteIcon?.classList.remove("fa-trash");
    }

    try {
        const confirmed = await Swal.fire({
            icon: "warning",
            title: "Hapus produk?",
            text: product
                ? `Hapus ${product.name}? Produk yang masih memiliki resep, kemasan, opsi, diskon, atau riwayat transaksi tidak dapat dihapus.`
                : `Hapus produk dengan ID ${productId}? Produk yang masih memiliki resep, kemasan, opsi, diskon, atau riwayat transaksi tidak dapat dihapus.`,
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
            confirmButtonColor: ckbColor("error"),
            customClass: { confirmButton: "ckb-swal-error-confirm" },
        });

        if (!confirmed.isConfirmed) return;

        const { response, data } = await apiFetch(
            `/api/products/${productId}`,
            {
                method: "DELETE",
            },
        );

        if (response.status === 404) {
            await fetchProducts();
            await Swal.fire({
                icon: "info",
                title: "Produk sudah tidak tersedia",
                text: "Daftar produk telah diperbarui dari server.",
                confirmButtonText: "Mengerti",
            });
            return;
        }

        if (!response.ok) {
            await showApiError(data, "Gagal Menghapus Produk");
            await fetchProducts();
            return;
        }

        const listRefreshed = await fetchProducts();

        await Swal.fire({
            icon: listRefreshed ? "success" : "warning",
            title: listRefreshed
                ? "Produk berhasil dihapus"
                : "Produk dihapus, daftar belum diperbarui",
            text: listRefreshed
                ? "Data produk telah disinkronkan dengan server."
                : "Penghapusan berhasil, tetapi daftar produk gagal dimuat. Silakan muat ulang.",
            confirmButtonText: "Mengerti",
        });
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await fetchProducts();
        await showApiError({
            message:
                error.message === "NETWORK_ERROR"
                    ? "Jaringan bermasalah. Produk tidak dapat dihapus saat ini."
                    : error.message || "Produk tidak dapat dihapus.",
        });
    } finally {
        pendingProductDeletes.delete(productId);

        if (deleteButton?.isConnected) {
            deleteButton.disabled = false;
            deleteButton.removeAttribute("aria-busy");
            deleteIcon?.classList.remove("fa-spinner", "fa-spin");
            deleteIcon?.classList.add("fa-trash");
        }
    }
}

export function initProductListEvents() {
    const productList = $("product-list");

    if (!productList || initializedProductLists.has(productList)) return;
    initializedProductLists.add(productList);

    $("btn-add-product")?.addEventListener("click", async (event) => {
        if (state.categories.length) return;

        event.preventDefault();

        await Swal.fire({
            icon: "warning",
            title: "Kategori belum tersedia",
            text: "Tambahkan kategori terlebih dahulu sebelum membuat produk.",
            confirmButtonText: "Mengerti",
        });
    });

    $("search-input")?.addEventListener("input", applyFilters);

    $("btn-refresh-products")?.addEventListener("click", async (event) => {
        await runRefreshAction(event.currentTarget, fetchProducts);
    });

    $("category-filter")?.addEventListener("change", applyFilters);

    $("status-filter")?.addEventListener("change", applyFilters);

    productList.addEventListener("click", async (event) => {
        const target = event.target;

        if (!(target instanceof Element)) return;

        const button = target.closest('button[data-action="delete"]');

        if (!button || !productList.contains(button) || button.disabled) return;

        await deleteProduct(button.dataset.id);
    });
}
