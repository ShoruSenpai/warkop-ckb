import { apiFetch } from "@/utils/api.js";
import { showApiError } from "@/utils/ui.js";

const root = document.querySelector("[data-purchase-detail]");

if (root) {
    const purchaseId = root.dataset.purchaseId;
    const statusControls = root.querySelector("[data-status-controls]");
    const statusSelect = root.querySelector("[data-status-select]");
    const statusSave = root.querySelector("[data-status-save]");
    let currentStatus = null;

    statusSave?.addEventListener("click", saveStatus);

    loadPurchase();

    async function loadPurchase() {
        try {
            const { response, data } = await apiFetch(
                `/api/reports/purchases/${encodeURIComponent(purchaseId)}`,
            );

            if (!response.ok || !data?.success) {
                throw new Error(
                    data?.message || "Detail invoice tidak dapat dimuat.",
                );
            }

            renderPurchase(data.data);
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;
            setState(error.message || "Detail invoice tidak dapat dimuat.");
        }
    }

    function renderPurchase(purchase) {
        currentStatus = purchase.status;
        if (statusControls) statusControls.hidden = currentStatus !== "pending";
        if (statusSelect) statusSelect.value = currentStatus;
        root.querySelectorAll("[data-purchase-field]").forEach((field) => {
            const key = field.dataset.purchaseField;
            field.textContent =
                key === "purchase_date"
                    ? formatDate(purchase[key])
                    : key === "status"
                      ? formatStatus(purchase[key])
                      : purchase[key] || "-";

            if (key === "status") {
                field.classList.add("inline-flex", "min-h-7", "items-center", "rounded-md", "px-2.5");
                field.classList.toggle("bg-ckb-secondary-container", purchase.status === "received");
                field.classList.toggle("text-ckb-on-secondary-container", purchase.status === "received");
                field.classList.toggle("bg-ckb-surface-container-low", purchase.status === "pending");
                field.classList.toggle("text-ckb-on-surface-variant", purchase.status === "pending");
            }
        });

        root.querySelector("[data-purchase-total]").textContent = formatRupiah(
            purchase.total_cost,
        );

        const itemsNode = root.querySelector("[data-invoice-items]");
        const itemTemplate = document.getElementById(
            "invoice-item-row-template",
        );
        const items = Array.isArray(purchase.items) ? purchase.items : [];
        itemsNode.replaceChildren();

        if (!items.length) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");
            cell.colSpan = 4;
            cell.className =
                "px-3 py-6 text-center text-xs text-ckb-on-surface-variant";
            cell.textContent = "Invoice ini belum memiliki rincian item.";
            row.append(cell);
            itemsNode.append(row);
        } else {
            items.forEach((item) => {
                const row =
                    itemTemplate.content.firstElementChild.cloneNode(true);
                const name =
                    item.item_name_snapshot ||
                    item.raw_material?.name ||
                    item.product?.name ||
                    "Barang tidak tersedia";
                const unit =
                    item.packaging_name_snapshot ||
                    item.purchase_unit ||
                    item.raw_material_packaging?.purchase_unit ||
                    item.product_packaging?.purchase_unit ||
                    "unit";
                setItemField(row, "item-name", name);
                setItemField(
                    row,
                    "item-quantity",
                    `${formatNumber(item.quantity)} ${unit}`,
                );
                setItemField(
                    row,
                    "item-unit-price",
                    formatRupiah(item.unit_price),
                );
                setItemField(row, "item-subtotal", formatRupiah(item.subtotal));
                itemsNode.append(row);
            });
        }

        root.querySelector("[data-detail-content]").hidden = false;
        setState(
            `${formatNumber(items.length)} item pada invoice ${purchase.invoice_number || ""}.`,
        );
    }

    async function saveStatus() {
        const nextStatus = statusSelect?.value;
        if (!nextStatus || nextStatus === currentStatus) return;

        const confirmation = await Swal.fire({
            icon: "question",
            title: "Ubah status pembelian?",
            text: nextStatus === "received"
                ? "Stok akan ditambahkan sesuai isi invoice."
                : "Status pembelian akan diubah.",
            showCancelButton: true,
            confirmButtonText: "Ya, ubah status",
            cancelButtonText: "Batal",
        });
        if (!confirmation.isConfirmed) return;

        statusSave.disabled = true;
        try {
            const { response, data } = await apiFetch(
                `/api/supplier-purchases/${encodeURIComponent(purchaseId)}/status`,
                { method: "PUT", body: { status: nextStatus } },
            );
            if (!response.ok || !data?.success) {
                await showApiError(data, "Status Tidak Berubah");
                statusSelect.value = currentStatus;
                return;
            }

            renderPurchase(data.data);
            await Swal.fire({
                icon: "success",
                title: "Status diperbarui",
                text: "Status invoice pembelian berhasil diperbarui.",
                confirmButtonText: "Selesai",
            });
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;
            console.error(error);
            await showApiError({ message: "Status pembelian tidak dapat diperbarui." });
        } finally {
            statusSave.disabled = false;
        }
    }

    function setItemField(row, name, value) {
        const field = row.querySelector(`[data-${name}]`);
        if (field) field.textContent = value;
    }

    function setState(message) {
        const state = root.querySelector("[data-detail-state]");
        if (state) state.textContent = message;
    }

    function formatRupiah(value) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    }

    function formatNumber(value) {
        return new Intl.NumberFormat("id-ID").format(Number(value) || 0);
    }

    function formatDate(value) {
        if (!value) return "-";
        const date = new Date(`${value}T00:00:00`);
        return Number.isNaN(date.getTime())
            ? "-"
            : date.toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "long",
                  year: "numeric",
              });
    }

    function formatStatus(status) {
        const labels = { received: "Diterima", pending: "Menunggu" };
        return labels[status] || status || "Status tidak diketahui";
    }
}
