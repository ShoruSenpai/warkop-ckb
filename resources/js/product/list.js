import { state } from "./state.js";
import { apiFetch } from "../utils/api.js";
import { $, escapeHtml, showApiError } from "./helper.js";

export async function fetchProducts() {
    try {
        const { response, data } = await apiFetch("/api/products");

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Produk");
            return;
        }

        state.products = Array.isArray(data.data) ? data.data : [];

        renderProducts(state.products);
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error("Gagal mengambil products:", error);

        const list = $("product-list");

        if (list) {
            list.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="px-4 py-10 text-center text-sm text-ckb-error"
                    >
                        Gagal memuat data produk.
                    </td>
                </tr>
            `;
        }
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
    const search = $("search-input").value.toLowerCase().trim();

    const category = $("category-filter").value;

    const status = $("status-filter").value;

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
                    class="px-4 py-10 text-center text-sm text-ckb-outline"
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
                    ? "bg-ckb-secondary-container/20 text-ckb-on-secondary-container"
                    : product.status === "sold_out"
                      ? "bg-ckb-error-container text-ckb-on-error-container"
                      : "bg-ckb-surface-container text-ckb-on-surface-variant";

            return `
                    <tr class="
                        border-b
                        border-ckb-outline-variant/20
                        text-sm
                        last:border-0
                    ">

                        <td class="px-4 py-4">

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
                                                    text-[11px] font-semibold
                                                    text-ckb-secondary
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
                            px-4 py-4
                            text-ckb-on-surface-variant
                        ">
                            ${
                                product.category?.name
                                    ? escapeHtml(product.category.name)
                                    : "—"
                            }
                        </td>

                        <td class="
                            px-4 py-4
                            font-semibold
                            text-ckb-primary
                        ">
                            Rp ${Number(product.base_price).toLocaleString(
                                "id-ID",
                            )}
                        </td>

                        <td class="px-4 py-4">

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
                            px-4 py-4
                            font-semibold
                            text-ckb-primary
                        ">
                            ${stock}
                        </td>

                        <td class="px-4 py-4">

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

                        <td class="px-4 py-4">

                            <div class="flex justify-end gap-2">

                                <a
                                    href="/products/${Number(product.id)}/edit"
                                    title="Edit Produk"
                                    class="
                                        flex h-8 w-8
                                        items-center justify-center
                                        rounded-lg
                                        text-ckb-primary
                                        hover:bg-ckb-primary-container
                                    "
                                >
                                    <i class="fas fa-pen"></i>
                                </a>

                                <button
                                    type="button"
                                    data-action="delete"
                                    data-id="${Number(product.id)}"
                                    title="Hapus Produk"
                                    class="
                                        flex h-8 w-8
                                        items-center justify-center
                                        rounded-lg
                                        text-ckb-error
                                        hover:bg-ckb-error-container
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
    const product = state.products.find(
        (item) => Number(item.id) === Number(id),
    );

    if (!product) return;

    const confirmed = await Swal.fire({
        icon: "warning",
        title: "Hapus produk?",
        text: "Produk yang memiliki data terkait atau riwayat transaksi tidak dapat dihapus.",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        confirmButtonColor: "#ba1a1a",
    });

    if (!confirmed.isConfirmed) {
        return;
    }

    try {
        const { response, data } = await apiFetch(`/api/products/${id}`, {
            method: "DELETE",
        });

        if (!response.ok) {
            await showApiError(data, "Gagal Menghapus Produk");

            return;
        }

        await Swal.fire({
            icon: "success",
            title: "Produk dihapus",
            text: "Produk berhasil dihapus.",
            timer: 1600,
            showConfirmButton: false,
        });

        await fetchProducts();
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: error.message || "Produk tidak dapat dihapus.",
        });
    }
}

export function initProductListEvents() {
    $("search-input")?.addEventListener("input", applyFilters);

    $("category-filter")?.addEventListener("change", applyFilters);

    $("status-filter")?.addEventListener("change", applyFilters);

    $("product-list")?.addEventListener("click", async (event) => {
        const button = event.target.closest("[data-action]");

        if (!button) return;

        if (button.dataset.action === "delete") {
            await deleteProduct(Number(button.dataset.id));
        }
    });
}
