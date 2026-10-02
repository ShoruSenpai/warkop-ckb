import { apiFetch } from "../utils/api.js";
import { fetchCategories } from "./list.js";
import { $, showApiError } from "./helper.js";

const modal = () => $("category-modal");
const form = () => $("category-form");
const input = () => $("category-name");
const errorElement = () => $("category-error");
const saveButton = () => $("btn-save-category");

function openCategoryModal() {
    const modalElement = modal();

    if (!modalElement) return;

    form()?.reset();

    if (errorElement()) {
        errorElement().textContent = "";
        errorElement().classList.add("hidden");
    }

    modalElement.classList.remove("hidden");
    input()?.focus();
}

function closeCategoryModal() {
    modal()?.classList.add("hidden");
}

function showCategoryError(message) {
    const element = errorElement();

    if (!element) return;

    element.textContent = message;
    element.classList.remove("hidden");
}

function setSavingState(isSaving) {
    const button = saveButton();

    if (!button) return;

    button.disabled = isSaving;

    button.innerHTML = isSaving
        ? '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...'
        : '<i class="fas fa-save mr-2"></i>Simpan';
}

async function saveCategory() {
    const name = input()?.value.trim();

    if (!name) {
        showCategoryError("Nama kategori wajib diisi.");
        input()?.focus();
        return;
    }

    if (name.length > 100) {
        showCategoryError("Nama kategori maksimal 100 karakter.");
        input()?.focus();
        return;
    }

    errorElement()?.classList.add("hidden");
    setSavingState(true);

    try {
        const { response, data } = await apiFetch("/api/categories", {
            method: "POST",
            body: {
                name,
            },
        });

        if (!response.ok) {
            await showApiError(
                response,
                data?.message || "Gagal menambahkan kategori.",
            );

            return;
        }

        // Refresh category data after successful creation.
        await fetchCategories();

        closeCategoryModal();

        await Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: data?.message || "Kategori berhasil ditambahkan.",
            timer: 1500,
            showConfirmButton: false,
        });
    } catch (error) {
        console.error("Gagal menambahkan kategori:", error);

        await Swal.fire({
            icon: "error",
            title: "Terjadi Kesalahan",
            text: "Kategori gagal ditambahkan.",
        });
    } finally {
        setSavingState(false);
    }
}

export function initCategoryEvents() {
    const addButton = $("btn-add-category");

    if (addButton) {
        addButton.addEventListener("click", openCategoryModal);
    }

    document.addEventListener("click", (event) => {
        const actionElement = event.target.closest("[data-action]");

        if (!actionElement) return;

        if (actionElement.dataset.action === "close-category-modal") {
            closeCategoryModal();
        }
    });

    const categoryForm = form();

    if (categoryForm) {
        categoryForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            await saveCategory();
        });
    }
}
