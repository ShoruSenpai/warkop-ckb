import { apiFetch } from "@/utils/api.js";

const root = document.querySelector("[data-purchase-invoices]");

if (root) {
    const list = root.querySelector("[data-invoice-list]");
    const periodControl = root.querySelector("[data-invoice-period]");
    const pagination = root.querySelector("[data-invoice-pagination]");
    const paginationStatus = root.querySelector("[data-pagination-status]");
    const previousButton = root.querySelector("[data-previous-page]");
    const nextButton = root.querySelector("[data-next-page]");
    const template = document.getElementById("invoice-row-template");
    const detailUrlTemplate = root.dataset.detailUrlTemplate;
    const cursors = [null];
    let cursorIndex = 0;
    let nextCursor = null;
    let previousCursor = null;

    periodControl?.addEventListener("change", () => {
        cursors.splice(1);
        cursorIndex = 0;
        nextCursor = null;
        previousCursor = null;
        loadInvoices();
    });

    previousButton?.addEventListener("click", () => {
        if (!previousCursor || cursorIndex === 0) return;
        cursorIndex -= 1;
        loadInvoices();
    });

    nextButton?.addEventListener("click", () => {
        if (!nextCursor) return;
        cursors[cursorIndex + 1] = nextCursor;
        cursorIndex += 1;
        loadInvoices();
    });

    loadInvoices();

    async function loadInvoices() {
        renderMessage("Memuat invoice pembelian...");
        list?.setAttribute("aria-busy", "true");

        try {
            const query = new URLSearchParams({
                period: periodControl?.value || "month",
                per_page: "20",
            });
            const cursor = cursors[cursorIndex];
            if (cursor) query.set("cursor", cursor);

            const { response, data } = await apiFetch(
                `/api/reports/purchases?${query}`,
            );

            if (!response.ok || !data?.success) {
                throw new Error(
                    data?.message || "Invoice pembelian tidak dapat dimuat.",
                );
            }

            renderInvoices(Array.isArray(data.data) ? data.data : []);
            nextCursor = data.meta?.next_cursor || null;
            previousCursor = data.meta?.previous_cursor || null;
            renderPagination(data.meta ?? {}, data.data?.length ?? 0);
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;

            renderMessage(
                error.message || "Invoice pembelian tidak dapat dimuat.",
            );
            root.querySelector("[data-invoice-summary]").textContent =
                "Gagal memuat invoice.";
            pagination.hidden = true;
        } finally {
            list?.setAttribute("aria-busy", "false");
        }
    }

    function renderInvoices(invoices) {
        if (!list) return;
        list.replaceChildren();

        if (!invoices.length) {
            renderMessage("Belum ada invoice pada periode ini.");
            root.querySelector("[data-invoice-summary]").textContent =
                "Belum ada pembelian pada periode terpilih.";
            return;
        }

        invoices.forEach((invoice) => {
            const row = template.content.firstElementChild.cloneNode(true);
            setField(row, "invoice_number", invoice.invoice_number || "-");
            setField(
                row,
                "supplier_name",
                invoice.supplier_name || "Nama supplier tidak tersedia",
            );
            setField(row, "purchase_date", formatDate(invoice.purchase_date));
            setField(
                row,
                "items_count",
                `${formatNumber(invoice.items_count)} item`,
            );
            setField(row, "total_cost", formatRupiah(invoice.total_cost));

            const link = row.querySelector("[data-invoice-link]");
            if (link && detailUrlTemplate) {
                link.href = detailUrlTemplate.replace(
                    "__PURCHASE__",
                    encodeURIComponent(invoice.id),
                );
            }

            list.append(row);
        });

        root.querySelector("[data-invoice-summary]").textContent =
            `${formatNumber(invoices.length)} invoice ditampilkan pada periode terpilih.`;
    }

    function renderPagination(meta, count) {
        if (!pagination) return;
        pagination.hidden = !nextCursor && cursorIndex === 0;
        if (paginationStatus) {
            paginationStatus.textContent = `${count} invoice pada halaman ini`;
        }
        if (previousButton)
            previousButton.disabled = cursorIndex === 0 || !previousCursor;
        if (nextButton) nextButton.disabled = !meta.has_more || !nextCursor;
    }

    function renderMessage(message) {
        if (!list) return;
        const cell = document.createElement("td");
        cell.colSpan = 6;
        cell.className =
            "px-3 py-6 text-center text-xs text-ckb-on-surface-variant";
        cell.textContent = message;
        const row = document.createElement("tr");
        row.append(cell);
        list.replaceChildren(row);
    }

    function setField(row, name, value) {
        const field = row.querySelector(`[data-field="${name}"]`);
        if (field) field.textContent = value;
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
                  month: "short",
                  year: "numeric",
              });
    }
}
