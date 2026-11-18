import { Chart } from "@/utils/chart.js";
import { ckbColor, ckbColorWithAlpha } from "@/utils/theme.js";

const root = document.querySelector("[data-sales-report]");

if (root) {
    const state = {
        period: "month",
        metric: "revenue",
        productMetric: "quantity",
        transactions: [],
        products: [],
        chartData: { labels: [], revenue: [], transactions: [] },
        page: 1,
        pageSize: 10,
    };
    const chartCanvas = root.querySelector("[data-sales-chart]");
    const chartPanel = root.querySelector("[data-chart-panel]");
    const chartStatus = root.querySelector("[data-chart-status]");
    const chartEmpty = root.querySelector("[data-chart-empty]");
    const reportCards = new Map(
        [...root.querySelectorAll("[data-sales-report-card]")].map((card) => [
            card.dataset.salesReportCard,
            card,
        ]),
    );
    const chartColors = {
        primary: ckbColor("primary"),
        accent: ckbColor("accent"),
        surface: ckbColor("surface"),
        muted: ckbColor("on-surface-variant"),
        grid: ckbColorWithAlpha("outline-variant", 0.55),
    };
    const chart = chartCanvas
        ? new Chart(chartCanvas, {
              type: "line",
              data: {
                  labels: [],
                  datasets: [
                      {
                          label: "Omzet",
                          data: [],
                          borderColor: chartColors.primary,
                          backgroundColor: ckbColorWithAlpha("primary", 0.12),
                          fill: true,
                          tension: 0.28,
                          spanGaps: false,
                          pointRadius: 2,
                          pointHoverRadius: 5,
                          pointBackgroundColor: chartColors.primary,
                      },
                  ],
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  interaction: { mode: "index", intersect: false },
                  plugins: {
                      legend: { display: false },
                      tooltip: {
                          enabled: true,
                          backgroundColor: chartColors.primary,
                          titleColor: chartColors.surface,
                          bodyColor: chartColors.surface,
                          padding: 10,
                          displayColors: false,
                          callbacks: {
                              label(context) {
                                  return state.metric === "revenue"
                                      ? formatRupiah(context.parsed.y)
                                      : `${formatNumber(context.parsed.y)} transaksi`;
                              },
                          },
                      },
                  },
                  scales: {
                      x: {
                          grid: { display: false },
                          border: { display: false },
                          ticks: {
                              color: chartColors.muted,
                              maxRotation: 0,
                              autoSkip: true,
                              maxTicksLimit: 8,
                              font: { size: 10 },
                          },
                      },
                      y: {
                          beginAtZero: true,
                          border: { display: false },
                          grid: { color: chartColors.grid },
                          ticks: {
                              color: chartColors.muted,
                              padding: 8,
                              maxTicksLimit: 5,
                              callback(value) {
                                  return state.metric === "revenue"
                                      ? formatCompactRupiah(value)
                                      : formatNumber(value);
                              },
                              font: { size: 10 },
                          },
                      },
                  },
              },
          })
        : null;

    root.addEventListener("change", (event) => {
        const target = event.target;

        if (
            target.matches(
                "[data-filter-start], [data-filter-end], [data-filter-status], [data-filter-payment], [data-filter-cashier], [data-page-size]",
            )
        ) {
            if (target.matches("[data-page-size]")) {
                state.pageSize = Number(target.value) || 10;
                state.page = 1;
            }

            renderTransactions();
        }

        if (target.matches("[data-period-start], [data-period-end]")) {
            requestPeriodUpdate();
        }
    });

    root.addEventListener("input", (event) => {
        if (event.target.matches("[data-filter-search]")) {
            state.page = 1;
            renderTransactions();
        }
    });

    root.addEventListener("click", (event) => {
        const filterControl = event.target.closest("[data-filter-group]");
        const filterGroup = filterControl?.dataset.filterGroup;
        const resetButton = event.target.closest("[data-reset-filters]");
        const previousButton = event.target.closest("[data-page-previous]");
        const nextButton = event.target.closest("[data-page-next]");
        const detailButton = event.target.closest("[data-transaction-detail]");
        const closeButton = event.target.closest("[data-detail-close]");

        if (filterGroup === "sales-trend-period") {
            state.period = filterControl.dataset.filterValue;
            setPressedState(
                `[data-filter-group="${filterGroup}"]`,
                filterControl,
            );
            root.querySelector("[data-custom-period]").hidden =
                state.period !== "custom";
            requestPeriodUpdate();
        }

        if (filterGroup === "sales-trend-metric") {
            state.metric = filterControl.dataset.filterValue;
            setPressedState(
                `[data-filter-group="${filterGroup}"]`,
                filterControl,
            );
            updateChartMetric();
        }

        if (filterGroup === "sales-top-products-metric") {
            state.productMetric = filterControl.dataset.filterValue;
            setPressedState(
                `[data-filter-group="${filterGroup}"]`,
                filterControl,
            );
            renderProducts();
        }

        if (resetButton) {
            root.querySelector("[data-filter-search]").value = "";
            root.querySelector("[data-filter-start]").value = "";
            root.querySelector("[data-filter-end]").value = "";
            root.querySelector("[data-filter-status]").value = "";
            root.querySelector("[data-filter-payment]").value = "";
            root.querySelector("[data-filter-cashier]").value = "";
            state.page = 1;
            renderTransactions();
        }

        if (previousButton && state.page > 1) {
            state.page -= 1;
            renderTransactions();
        }

        if (nextButton) {
            state.page += 1;
            renderTransactions();
        }

        if (detailButton) {
            const transaction = state.transactions.find(
                (item) =>
                    String(item.id) === detailButton.dataset.transactionId,
            );

            if (transaction) openTransactionDetail(transaction);
        }

        if (closeButton) {
            root.querySelector("[data-transaction-dialog]").close();
        }
    });

    root.querySelector("[data-transaction-dialog]")?.addEventListener(
        "click",
        (event) => {
            if (event.target === event.currentTarget)
                event.currentTarget.close();
        },
    );

    root.addEventListener("sales-report:data", (event) => {
        const { target, data = {} } = event.detail ?? {};
        const card = findReportCard(target);

        if (!card) return;

        card.setAttribute("aria-busy", "false");

        if (target === "metrics") {
            const metrics = { ...(data.metrics ?? data) };

            if (
                !metrics.average_ticket &&
                metrics.total_sales &&
                metrics.transaction_count
            ) {
                metrics.average_ticket = {
                    current:
                        Number(metrics.total_sales.current) /
                        Number(metrics.transaction_count.current),
                    previous:
                        Number(metrics.total_sales.previous) /
                        Number(metrics.transaction_count.previous),
                };
            }

            renderMetrics(metrics);
            return;
        }

        if (target === "trend-chart") {
            state.chartData = {
                labels: Array.isArray(data.labels) ? data.labels : [],
                revenue: Array.isArray(data.revenue) ? data.revenue : [],
                transactions: Array.isArray(data.transactions)
                    ? data.transactions
                    : [],
            };
            root.querySelector("[data-chart-error]").hidden = true;
            chartPanel?.setAttribute("data-chart-state", "empty");
            const emptyMessage = root.querySelector(
                "[data-chart-empty-message]",
            );
            if (emptyMessage) {
                emptyMessage.textContent =
                    "Grafik akan menampilkan data setelah sumber transaksi tersedia.";
                emptyMessage.classList.remove(
                    "bg-ckb-error-container",
                    "text-ckb-on-error-container",
                );
            }
            updateChartPeriod();
            return;
        }

        if (target === "top-products") {
            state.products = Array.isArray(data.products)
                ? data.products.slice(0, 5)
                : [];
            renderProducts();
            return;
        }

        if (target === "transaction-history") {
            state.transactions = Array.isArray(data.transactions)
                ? data.transactions
                : [];
            root.querySelector("[data-transaction-panel]").hidden = false;
            root.querySelector("[data-transaction-loading]").classList.add(
                "hidden",
            );
            root
                .querySelector("[data-transaction-error]")
                .classList.add("hidden");
            renderTransactions();
        }
    });

    root.addEventListener("sales-report:loading", (event) => {
        const card = findReportCard(event.detail?.target);
        if (!card) return;

        card.setAttribute("aria-busy", "true");

        if (event.detail.target === "trend-chart" && chartStatus) {
            chartStatus.textContent = "Memuat tren penjualan...";
            chartPanel?.setAttribute("aria-busy", "true");
            chartPanel?.setAttribute("data-chart-state", "loading");
            root.querySelector("[data-chart-error]").hidden = true;
        }

        if (event.detail.target === "transaction-history") {
            root.querySelector("[data-transaction-panel]").hidden = true;
            root
                .querySelector("[data-transaction-loading]")
                .classList.remove("hidden");
            root
                .querySelector("[data-transaction-error]")
                .classList.add("hidden");
        }
    });

    root.addEventListener("sales-report:error", (event) => {
        const target = event.detail?.target;
        const card = findReportCard(target);
        if (!card) return;

        const message = event.detail?.message || "Data tidak dapat dimuat.";
        card.setAttribute("aria-busy", "false");

        if (target === "metrics") resetMetrics();

        if (target === "top-products") {
            state.products = [];
            renderProducts();
            const summary = root.querySelector("[data-product-list-summary]");
            if (summary) summary.textContent = message;
        }

        if (target === "trend-chart") {
            chartPanel?.setAttribute("data-chart-state", "error");
            chartPanel?.setAttribute("aria-busy", "false");
            if (chartStatus) chartStatus.textContent = message;
            const chartEmptyMessage = root.querySelector(
                "[data-chart-empty-message]",
            );
            if (chartEmptyMessage) chartEmptyMessage.textContent = message;
            if (chartEmpty) chartEmpty.hidden = false;
            root.querySelector("[data-chart-error]").hidden = false;
        }

        if (target === "transaction-history") {
            root.querySelector("[data-transaction-panel]").hidden = true;
            root
                .querySelector("[data-transaction-loading]")
                .classList.add("hidden");
            root.querySelector("[data-transaction-error]").textContent =
                message;
            root
                .querySelector("[data-transaction-error]")
                .classList.remove("hidden");
        }
    });

    updateChartPeriod();
    renderProducts();
    renderTransactions();

    function updateChartPeriod() {
        if (!chart) return;

        const labels = state.chartData.labels.length
            ? state.chartData.labels
            : periodLabels(
                  state.period,
                  root.querySelector("[data-period-start]").value,
                  root.querySelector("[data-period-end]").value,
              );
        const values = state.chartData[state.metric];

        chart.data.labels = labels;
        chart.data.datasets[0].label =
            state.metric === "revenue" ? "Omzet" : "Jumlah transaksi";
        chart.data.datasets[0].data = values.length
            ? values
            : labels.map(() => null);
        chart.data.datasets[0].borderColor =
            state.metric === "revenue"
                ? chartColors.primary
                : chartColors.accent;
        chart.data.datasets[0].backgroundColor = ckbColorWithAlpha(
            state.metric === "revenue" ? "primary" : "accent",
            0.12,
        );
        chart.data.datasets[0].pointBackgroundColor =
            chart.data.datasets[0].borderColor;
        chart.options.scales.y.ticks.callback = (value) =>
            state.metric === "revenue"
                ? formatCompactRupiah(value)
                : formatNumber(value);
        chart.update();

        const hasData = chart.data.datasets[0].data.some(
            (value) =>
                value !== null &&
                value !== undefined &&
                value !== "" &&
                Number.isFinite(Number(value)),
        );
        const isError = chartPanel?.dataset.chartState === "error";
        chartPanel?.setAttribute(
            "data-chart-state",
            hasData ? "ready" : "empty",
        );
        chartPanel?.setAttribute("aria-busy", "false");
        if (chartEmpty) chartEmpty.hidden = hasData || isError;
        if (chartStatus && !isError) {
            chartStatus.textContent = hasData
                ? `${chart.data.labels.length} interval pada periode terpilih.`
                : "Belum ada data penjualan pada periode ini.";
        }
    }

    function updateChartMetric() {
        const title = root.querySelector("[data-chart-title]");
        if (title) {
            title.textContent =
                state.metric === "revenue"
                    ? "Omzet penjualan"
                    : "Jumlah transaksi";
        }

        updateChartPeriod();
    }

    function requestPeriodUpdate() {
        state.chartData = { labels: [], revenue: [], transactions: [] };
        chartPanel?.setAttribute("data-chart-state", "empty");
        root.querySelector("[data-chart-error]").hidden = true;
        const emptyMessage = root.querySelector("[data-chart-empty-message]");
        if (emptyMessage) {
            emptyMessage.textContent =
                "Grafik akan menampilkan data setelah sumber transaksi tersedia.";
            emptyMessage.classList.remove(
                "bg-ckb-error-container",
                "text-ckb-on-error-container",
            );
        }
        updateChartPeriod();
        root.dispatchEvent(
            new CustomEvent("sales-report:period-change", {
                detail: {
                    target: "trend-chart",
                    period: state.period,
                    start: root.querySelector("[data-period-start]").value,
                    end: root.querySelector("[data-period-end]").value,
                },
            }),
        );
    }

    function findReportCard(target) {
        return reportCards.get(target);
    }

    function resetMetrics() {
        root.querySelectorAll("[data-sales-kpi-value]").forEach((node) => {
            node.textContent = "—";
        });
        root.querySelectorAll("[data-sales-kpi-note]").forEach((node) => {
            node.textContent = "Data periode ini belum tersedia.";
        });
        root.querySelectorAll("[data-sales-kpi-delta]").forEach((node) => {
            node.textContent = "Perbandingan belum tersedia";
            node.classList.remove("text-ckb-success", "text-ckb-error");
            node.classList.add("text-ckb-on-surface-variant");
        });
    }

    function renderMetrics(metrics) {
        const entries = [
            ["sales_total", metrics.total_sales],
            ["sales_count", metrics.transaction_count],
            ["sales_average", metrics.average_ticket],
        ];

        entries.forEach(([key, metric]) => {
            if (
                !metric ||
                metric.current === null ||
                metric.current === undefined ||
                !Number.isFinite(Number(metric.current))
            ) {
                return;
            }

            const valueNode = root.querySelector(
                `[data-sales-kpi-value="${key}"]`,
            );
            const noteNode = root.querySelector(
                `[data-sales-kpi-note="${key}"]`,
            );
            const deltaNode = root.querySelector(
                `[data-sales-kpi-delta="${key}"]`,
            );
            const isCurrency = key !== "sales_count";

            if (valueNode) {
                valueNode.textContent = isCurrency
                    ? formatRupiah(metric.current)
                    : formatNumber(metric.current);
            }

            if (noteNode)
                noteNode.textContent = metric.note || "Pada periode terpilih";

            if (
                deltaNode &&
                metric.previous !== null &&
                metric.previous !== undefined &&
                Number.isFinite(Number(metric.previous))
            ) {
                const change = Number(metric.previous)
                    ? ((Number(metric.current) - Number(metric.previous)) /
                          Number(metric.previous)) *
                      100
                    : null;

                deltaNode.textContent =
                    change === null
                        ? "Belum ada pembanding"
                        : `${change > 0 ? "+" : ""}${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 1 }).format(change)}% dari periode lalu`;
                deltaNode.classList.toggle(
                    "text-ckb-success",
                    change !== null && change >= 0,
                );
                deltaNode.classList.toggle(
                    "text-ckb-error",
                    change !== null && change < 0,
                );
                deltaNode.classList.toggle(
                    "text-ckb-on-surface-variant",
                    change === null,
                );
            }
        });
    }

    function renderProducts() {
        const list = root.querySelector("[data-top-products]");
        const summary = root.querySelector("[data-product-list-summary]");
        if (!list) return;

        const products = [...state.products].sort((first, second) =>
            state.productMetric === "revenue"
                ? Number(second.revenue) - Number(first.revenue)
                : Number(second.quantity) - Number(first.quantity),
        );
        list.replaceChildren();

        if (summary) {
            summary.textContent =
                state.productMetric === "revenue"
                    ? "Peringkat berdasarkan kontribusi omzet"
                    : "Peringkat berdasarkan jumlah terjual";
        }

        if (!products.length) {
            const empty = document.createElement("li");
            empty.className =
                "flex min-h-40 flex-col items-center justify-center px-3 py-6 text-center";
            empty.innerHTML = `<i class="fas fa-chart-line mb-3 text-xl text-ckb-outline" aria-hidden="true"></i><p class="text-xs font-semibold text-ckb-primary">Belum ada data produk terjual.</p><p class="mt-1 text-xs text-ckb-on-surface-variant">Daftar peringkat akan muncul saat sumber transaksi tersedia.</p>`;
            list.append(empty);
            return;
        }

        products.slice(0, 5).forEach((product, index) => {
            const row = document.createElement("li");
            row.className = "flex items-center gap-3 py-3";

            const rank = document.createElement("span");
            rank.className =
                "flex size-8 shrink-0 items-center justify-center rounded-lg bg-ckb-secondary-container text-xs font-bold text-ckb-on-secondary-container";
            rank.textContent = String(index + 1);

            const details = document.createElement("div");
            details.className = "min-w-0 flex-1";

            const name = document.createElement("p");
            name.className = "truncate text-xs font-semibold text-ckb-primary";
            name.textContent = product.name || "Nama produk tidak tersedia";

            const quantity = document.createElement("p");
            quantity.className = "mt-1 text-[10px] text-ckb-on-surface-variant";
            quantity.textContent = `${formatNumber(product.quantity)} terjual${product.unit ? ` · ${product.unit}` : ""}`;
            details.append(name, quantity);

            const value = document.createElement("p");
            value.className =
                "shrink-0 text-right text-xs font-semibold text-ckb-primary";
            value.textContent =
                state.productMetric === "revenue"
                    ? formatRupiah(product.revenue)
                    : "";

            row.append(rank, details, value);
            list.append(row);
        });
    }

    function renderTransactions() {
        const list = root.querySelector("[data-transaction-list]");
        const template = document.getElementById(
            "sales-transaction-row-template",
        );
        const filtered = filteredTransactions();
        renderCashierOptions();
        const totalPages = Math.max(
            1,
            Math.ceil(filtered.length / state.pageSize),
        );
        state.page = Math.min(state.page, totalPages);
        const startIndex = (state.page - 1) * state.pageSize;
        const pageItems = filtered.slice(
            startIndex,
            startIndex + state.pageSize,
        );
        const emptyRow = root.querySelector("[data-transaction-empty]");

        if (!list || !template) return;

        list.querySelectorAll("[data-transaction-row]").forEach((row) =>
            row.remove(),
        );
        if (emptyRow) emptyRow.hidden = pageItems.length > 0;

        pageItems.forEach((transaction) => {
            const row = template.content.firstElementChild.cloneNode(true);
            const values = {
                transaction_time: formatDateTime(transaction.transaction_time),
                invoice_code: transaction.invoice_code || "—",
                cashier:
                    transaction.cashier?.name ||
                    transaction.cashier_name ||
                    "—",
                order_type: transaction.order_type || "—",
                payment_method: formatPaymentMethod(transaction.payment_method),
                final_amount: formatRupiah(transaction.final_amount),
            };

            Object.entries(values).forEach(([field, value]) => {
                const node = row.querySelector(
                    `[data-transaction-field="${field}"]`,
                );
                if (node) node.textContent = value;
            });

            const statusNode = row.querySelector(
                '[data-transaction-field="payment_status"]',
            );
            if (statusNode) {
                statusNode.textContent = formatPaymentStatus(
                    transaction.payment_status,
                );
                statusNode.classList.toggle(
                    "bg-ckb-success-container",
                    transaction.payment_status === "paid",
                );
                statusNode.classList.toggle(
                    "text-ckb-on-success-container",
                    transaction.payment_status === "paid",
                );
                statusNode.classList.toggle(
                    "bg-ckb-secondary-container",
                    transaction.payment_status !== "paid",
                );
                statusNode.classList.toggle(
                    "text-ckb-on-secondary-container",
                    transaction.payment_status !== "paid",
                );
            }

            const detailButton = row.querySelector("[data-transaction-detail]");
            detailButton.dataset.transactionId = String(transaction.id);
            list.append(row);
        });

        const summary = root.querySelector("[data-transaction-summary]");
        const paginationStatus = root.querySelector("[data-pagination-status]");
        const previousButton = root.querySelector("[data-page-previous]");
        const nextButton = root.querySelector("[data-page-next]");
        const currentFilters = hasTransactionFilters();

        if (summary) {
            summary.textContent = filtered.length
                ? `${formatNumber(filtered.length)} transaksi sesuai filter.`
                : currentFilters && state.transactions.length
                  ? "Tidak ada transaksi yang sesuai dengan filter."
                  : "Data transaksi belum tersedia dari backend.";
        }

        if (paginationStatus) {
            paginationStatus.textContent = filtered.length
                ? `Menampilkan ${startIndex + 1}–${Math.min(startIndex + state.pageSize, filtered.length)} dari ${formatNumber(filtered.length)} transaksi`
                : "0 transaksi";
        }

        if (previousButton) previousButton.disabled = state.page <= 1;
        if (nextButton) nextButton.disabled = state.page >= totalPages;
        root.dataset.reportState = state.transactions.length
            ? "ready"
            : "empty";
    }

    function renderCashierOptions() {
        const select = root.querySelector("[data-filter-cashier]");
        if (!select) return;

        const selected = select.value;
        const cashiers = new Map();

        state.transactions.forEach((transaction) => {
            const cashierId = transaction.cashier_id ?? transaction.cashier?.id;
            const cashierName =
                transaction.cashier?.name || transaction.cashier_name;
            const cashierValue = cashierId ?? cashierName;
            if (cashierValue && cashierName) {
                cashiers.set(String(cashierValue), cashierName);
            }
        });

        select.replaceChildren(new Option("Semua kasir", ""));
        [...cashiers.entries()]
            .sort((first, second) => first[1].localeCompare(second[1], "id"))
            .forEach(([id, name]) => select.add(new Option(name, id)));
        select.value = cashiers.has(selected) ? selected : "";
    }

    function filteredTransactions() {
        const search = root
            .querySelector("[data-filter-search]")
            .value.trim()
            .toLocaleLowerCase("id-ID");
        const start = root.querySelector("[data-filter-start]").value;
        const end = root.querySelector("[data-filter-end]").value;
        const status = root.querySelector("[data-filter-status]").value;
        const payment = root.querySelector("[data-filter-payment]").value;
        const cashier = root.querySelector("[data-filter-cashier]").value;

        return state.transactions.filter((transaction) => {
            const cashierName =
                transaction.cashier?.name || transaction.cashier_name || "";
            const searchMatches =
                !search ||
                `${transaction.invoice_code || ""} ${cashierName}`
                    .toLocaleLowerCase("id-ID")
                    .includes(search);
            const transactionDate =
                transaction.transaction_time?.slice(0, 10) || "";

            return (
                searchMatches &&
                (!start || transactionDate >= start) &&
                (!end || transactionDate <= end) &&
                (!status || transaction.payment_status === status) &&
                (!payment || transaction.payment_method === payment) &&
                (!cashier ||
                    String(
                        transaction.cashier_id ??
                            transaction.cashier?.id ??
                            transaction.cashier?.name ??
                            transaction.cashier_name ??
                            "",
                    ) === cashier)
            );
        });
    }

    function hasTransactionFilters() {
        return Boolean(
            root.querySelector("[data-filter-search]").value ||
            root.querySelector("[data-filter-start]").value ||
            root.querySelector("[data-filter-end]").value ||
            root.querySelector("[data-filter-status]").value ||
            root.querySelector("[data-filter-payment]").value ||
            root.querySelector("[data-filter-cashier]").value,
        );
    }

    function openTransactionDetail(transaction) {
        const dialog = root.querySelector("[data-transaction-dialog]");
        const fields = {
            transaction_time: formatDateTime(transaction.transaction_time),
            cashier:
                transaction.cashier?.name || transaction.cashier_name || "—",
            order_type: transaction.order_type || "—",
            payment_method: formatPaymentMethod(transaction.payment_method),
            payment_status: formatPaymentStatus(transaction.payment_status),
            discount_amount: formatRupiah(transaction.discount_amount),
        };

        root.querySelector("[data-detail-invoice]").textContent =
            transaction.invoice_code || "Nomor invoice tidak tersedia";

        Object.entries(fields).forEach(([field, value]) => {
            const node = root.querySelector(`[data-detail-field="${field}"]`);
            if (node) node.textContent = value;
        });

        const items = root.querySelector("[data-detail-items]");
        const itemTemplate = document.getElementById(
            "sales-detail-item-template",
        );
        items.replaceChildren();

        (Array.isArray(transaction.items) ? transaction.items : []).forEach(
            (item) => {
                const row =
                    itemTemplate.content.firstElementChild.cloneNode(true);
                const values = {
                    name:
                        item.product?.name ||
                        item.product_name ||
                        "Nama produk tidak tersedia",
                    quantity: formatNumber(item.quantity),
                    unit_price: formatRupiah(
                        Number(item.unit_price || 0) +
                            Number(item.option_price || 0),
                    ),
                    subtotal: formatRupiah(item.subtotal),
                };

                Object.entries(values).forEach(([field, value]) => {
                    const node = row.querySelector(
                        `[data-detail-item="${field}"]`,
                    );
                    if (node) node.textContent = value;
                });

                items.append(row);
            },
        );

        if (!items.childElementCount) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");
            cell.colSpan = 4;
            cell.className =
                "px-3 py-6 text-center text-xs text-ckb-on-surface-variant";
            cell.textContent = "Rincian produk tidak tersedia.";
            row.append(cell);
            items.append(row);
        }

        root.querySelector("[data-detail-subtotal]").textContent =
            `Subtotal ${formatRupiah(transaction.gross_amount)}`;
        root.querySelector("[data-detail-gross]").textContent = formatRupiah(
            transaction.gross_amount,
        );
        root.querySelector("[data-detail-discount]").textContent = formatRupiah(
            transaction.discount_amount,
        );
        root.querySelector("[data-detail-total]").textContent = formatRupiah(
            transaction.final_amount,
        );

        dialog?.showModal();
    }

    function setPressedState(selector, activeControl) {
        root.querySelectorAll(selector).forEach((control) => {
            const active = control === activeControl;
            control.setAttribute("aria-pressed", String(active));
            control.classList.toggle("bg-ckb-primary", active);
            control.classList.toggle("text-ckb-on-primary", active);
            control.classList.toggle("shadow-surface", active);
            control.classList.toggle(
                "text-ckb-on-secondary-container",
                !active,
            );
        });
    }

    function periodLabels(period, startValue, endValue) {
        if (period === "today") {
            return Array.from(
                { length: 24 },
                (_, hour) => `${String(hour).padStart(2, "0")}:00`,
            );
        }

        if (period === "week") {
            const today = startOfDay(new Date());
            const mondayOffset = (today.getDay() + 6) % 7;
            const monday = new Date(today);
            monday.setDate(today.getDate() - mondayOffset);

            return Array.from({ length: 7 }, (_, index) => {
                const date = new Date(monday);
                date.setDate(monday.getDate() + index);
                return new Intl.DateTimeFormat("id-ID", {
                    weekday: "short",
                }).format(date);
            });
        }

        if (period === "month") {
            const now = new Date();
            return Array.from(
                {
                    length: new Date(
                        now.getFullYear(),
                        now.getMonth() + 1,
                        0,
                    ).getDate(),
                },
                (_, index) => String(index + 1),
            );
        }

        if (period === "year") {
            return Array.from({ length: 12 }, (_, index) =>
                new Intl.DateTimeFormat("id-ID", { month: "short" }).format(
                    new Date(new Date().getFullYear(), index, 1),
                ),
            );
        }

        const start = parseDateInput(startValue);
        const end = parseDateInput(endValue);
        if (!start || !end || end < start) return [];

        const dayCount = Math.floor((end - start) / 86400000) + 1;
        if (dayCount === 1) {
            return Array.from(
                { length: 24 },
                (_, hour) => `${String(hour).padStart(2, "0")}:00`,
            );
        }

        if (dayCount <= 62) {
            return Array.from({ length: dayCount }, (_, index) => {
                const date = new Date(start);
                date.setDate(start.getDate() + index);
                return new Intl.DateTimeFormat("id-ID", {
                    day: "numeric",
                    month: "short",
                }).format(date);
            });
        }

        const labels = [];
        const cursor = new Date(start.getFullYear(), start.getMonth(), 1);
        while (cursor <= end) {
            labels.push(
                new Intl.DateTimeFormat("id-ID", {
                    month: "short",
                    year: "2-digit",
                }).format(cursor),
            );
            cursor.setMonth(cursor.getMonth() + 1);
        }
        return labels;
    }

    function parseDateInput(value) {
        if (!value) return null;
        const [year, month, day] = value.split("-").map(Number);
        return new Date(year, month - 1, day);
    }

    function startOfDay(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function formatRupiah(value) {
        if (value === null || value === undefined || value === "") return "—";

        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    }

    function formatCompactRupiah(value) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            notation: "compact",
            maximumFractionDigits: 1,
        }).format(Number(value) || 0);
    }

    function formatNumber(value) {
        if (value === null || value === undefined || value === "") return "—";

        return new Intl.NumberFormat("id-ID").format(Number(value) || 0);
    }

    function formatDateTime(value) {
        if (!value) return "—";
        const date = new Date(value);
        return Number.isNaN(date.getTime())
            ? "—"
            : new Intl.DateTimeFormat("id-ID", {
                  dateStyle: "medium",
                  timeStyle: "short",
              }).format(date);
    }

    function formatPaymentMethod(value) {
        return (
            {
                cash: "Tunai",
                qris: "QRIS",
                transfer: "Transfer",
            }[value] ||
            value ||
            "—"
        );
    }

    function formatPaymentStatus(value) {
        return value === "paid" ? "Lunas" : value || "—";
    }
}
