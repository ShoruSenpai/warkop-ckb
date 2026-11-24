import { Chart } from "@/utils/chart.js";
import { ckbColor, ckbColorWithAlpha } from "@/utils/theme.js";
import { apiFetch } from "@/utils/api.js";
import { downloadReportPdf } from "@/utils/report-download.js";

const root = document.querySelector("[data-sales-report]");

if (root) {
    const metricKeys = ["sales_total", "sales_count", "sales_average"];
    const metricApiKeys = {
        sales_total: "total_sales",
        sales_count: "transaction_count",
        sales_average: "average_ticket",
    };
    const state = {
        period: "month",
        metricPeriod: "month",
        metric: "revenue",
        productMetric: "quantity",
        transactions: [],
        products: [],
        chartData: { labels: [], revenue: [], transactions: [] },
        page: 1,
        pageSize: 10,
        draftPageSize: 10,
        appliedFilters: {
            start_date: "",
            end_date: "",
            status: "",
            payment_status: "",
            payment_method: "",
            cashier_id: "",
            search: "",
        },
        transactionLoading: false,
        pdfExporting: false,
        meta: { total: 0, last_page: 1, has_more: false, current_page: 1 },
    };
    const requests = {
        metrics: 0,
        "trend-chart": 0,
        "top-products": 0,
        "transaction-history": 0,
        detail: 0,
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

        if (target.matches("[data-filter-control], [data-page-size]")) {
            if (target.matches("[data-page-size]")) {
                state.draftPageSize = Number(target.value) || 10;
            }
            updateDraftFilterNotice();
        }

        if (target.matches("[data-period-start], [data-period-end]")) {
            requestPeriodUpdate();
        }
    });

    root.addEventListener("input", (event) => {
        if (event.target.matches("[data-filter-control]")) {
            updateDraftFilterNotice();
        }
    });

    root.addEventListener("click", (event) => {
        const filterControl = event.target.closest("[data-filter-group]");
        const filterGroup = filterControl?.dataset.filterGroup;
        const resetButton = event.target.closest("[data-reset-filters]");
        const applyFiltersButton = event.target.closest("[data-apply-filters]");
        const previousButton = event.target.closest("[data-page-previous]");
        const nextButton = event.target.closest("[data-page-next]");
        const detailButton = event.target.closest("[data-transaction-detail]");
        const retryButton = event.target.closest("[data-sales-retry]");
        const exportButton = event.target.closest("[data-sales-export]");
        const closeButton = event.target.closest("[data-detail-close]");

        if (exportButton && !state.pdfExporting) {
            state.pdfExporting = true;
            const params = new URLSearchParams({
                sales_total_period: state.metricPeriod,
                sales_count_period: state.metricPeriod,
                sales_average_period: state.metricPeriod,
                chart_period: state.period,
                chart_metric: state.metric,
                ...state.appliedFilters,
            });

            if (state.period === "custom") {
                params.set(
                    "chart_start_date",
                    root.querySelector("[data-period-start]").value,
                );
                params.set(
                    "chart_end_date",
                    root.querySelector("[data-period-end]").value,
                );
            }

            downloadReportPdf(
                exportButton,
                `/reports/sales/pdf?${params}`,
                "penjualan",
            ).finally(() => {
                state.pdfExporting = false;
            });
        }

        if (filterGroup === "sales-metrics-period") {
            state.metricPeriod = filterControl.dataset.filterValue;
            setPressedState(
                `[data-filter-group="${filterGroup}"]`,
                filterControl,
            );
            loadSummarySection("metrics", "metrics", state.metricPeriod);
        }

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

        if (resetButton && !state.transactionLoading) {
            root.querySelector("[data-filter-search]").value = "";
            root.querySelector("[data-filter-start]").value = "";
            root.querySelector("[data-filter-end]").value = "";
            root.querySelector("[data-filter-status]").value = "";
            root.querySelector("[data-filter-payment-status]").value = "";
            root.querySelector("[data-filter-payment]").value = "";
            root.querySelector("[data-filter-cashier]").value = "";
            root.querySelector("[data-page-size]").value = "10";
            state.draftPageSize = 10;
            state.appliedFilters = readDraftFilters();
            state.pageSize = 10;
            state.page = 1;
            root.querySelector("[data-filter-draft-notice]").hidden = true;
            loadTransactions();
        }

        if (applyFiltersButton && !state.transactionLoading) {
            state.appliedFilters = readDraftFilters();
            state.pageSize = state.draftPageSize;
            state.page = 1;
            root.querySelector("[data-filter-draft-notice]").hidden = true;
            loadTransactions();
        }

        if (previousButton && state.page > 1) {
            state.page -= 1;
            loadTransactions();
        }

        if (nextButton && state.meta.has_more && !state.transactionLoading) {
            state.page += 1;
            loadTransactions();
        }

        if (detailButton) {
            loadTransactionDetail(detailButton.dataset.transactionId);
        }

        const detailRetryButton = event.target.closest("[data-detail-retry]");
        if (detailRetryButton) {
            loadTransactionDetail(detailRetryButton.dataset.detailRetry);
        }

        if (retryButton) {
            const target = retryButton.dataset.salesRetry;
            if (target === "transaction-history") loadTransactions();
            if (metricKeys.includes(target))
                loadSummarySection("metrics", "metrics", state.metricPeriod);
            if (target === "trend-chart")
                loadSummarySection("chart", "trend-chart", state.period);
            if (target === "top-products")
                loadSummarySection(
                    "top-products",
                    "top-products",
                    state.period,
                );
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
    root.querySelector("[data-transaction-dialog]")?.addEventListener(
        "close",
        () => {
            requests.detail += 1;
        },
    );

    root.addEventListener("sales-report:data", (event) => {
        const { target, data = {}, meta = {} } = event.detail ?? {};
        const card = findReportCard(target);

        if (metricKeys.includes(target)) {
            renderMetric(
                target,
                data.metrics?.[metricApiKeys[target]],
                meta.periodRange,
            );
            const retry = card?.querySelector("[data-sales-retry]");
            if (retry) retry.hidden = true;
            return;
        }

        if (target === "metrics") {
            metricKeys.forEach((key) => {
                renderMetric(
                    key,
                    data.metrics?.[metricApiKeys[key]],
                    meta.periodRange,
                );
                const retry = root.querySelector(
                    `[data-sales-retry="${key}"]`,
                );
                if (retry) retry.hidden = true;
            });
            return;
        }

        if (!card) return;
        card.setAttribute("aria-busy", "false");

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
            root.querySelector('[data-sales-retry="top-products"]').hidden =
                true;
            const summary = root.querySelector("[data-product-list-summary]");
            if (summary) summary.textContent = "Maksimal lima produk";
            state.products = Array.isArray(data.products)
                ? data.products.slice(0, 5)
                : [];
            renderProducts();
            return;
        }

        if (target === "transaction-history") {
            state.transactionLoading = false;
            state.transactions = Array.isArray(data.transactions)
                ? data.transactions
                : [];
            state.meta = meta;
            updateTransactionFilterOptions(meta.filters ?? {});
            root.querySelector("[data-transaction-panel]").hidden = false;
            root.querySelector("[data-transaction-loading]").classList.add(
                "hidden",
            );
            root.querySelector("[data-transaction-error]").classList.add(
                "hidden",
            );
            renderTransactions();
        }
    });

    root.addEventListener("sales-report:loading", (event) => {
        const { target, meta = {} } = event.detail ?? {};
        const card = findReportCard(target);

        if (metricKeys.includes(target)) {
            setMetricBusy(target, true);
            const retry = card?.querySelector("[data-sales-retry]");
            if (retry) retry.hidden = true;
            return;
        }
        if (target === "metrics") {
            metricKeys.forEach((key) => {
                setMetricBusy(key, true);
                const retry = root.querySelector(
                    `[data-sales-retry="${key}"]`,
                );
                if (retry) retry.hidden = true;
            });
            return;
        }
        if (!card) return;
        card.setAttribute("aria-busy", "true");

        if (event.detail.target === "trend-chart" && chartStatus) {
            chartStatus.textContent = "Memuat tren penjualan...";
            chartPanel?.setAttribute("aria-busy", "true");
            chartPanel?.setAttribute("data-chart-state", "loading");
            root.querySelector("[data-chart-error]").hidden = true;
        }

        if (event.detail.target === "top-products") {
            root.querySelector('[data-sales-retry="top-products"]').hidden =
                true;
            const summary = root.querySelector("[data-product-list-summary]");
            if (summary) summary.textContent = "Memuat produk terlaris...";
        }

        if (event.detail.target === "transaction-history") {
            state.transactionLoading = true;
            root.querySelector("[data-apply-filters]").disabled = true;
            root.querySelector("[data-reset-filters]").disabled = true;
            root.querySelector("[data-transaction-panel]").hidden = true;
            root.querySelector("[data-transaction-loading]").classList.remove(
                "hidden",
            );
            root.querySelector("[data-transaction-error]").classList.add(
                "hidden",
            );
        }
    });

    root.addEventListener("sales-report:error", (event) => {
        const { target, message: errorMessage, meta = {} } = event.detail ?? {};
        const card = findReportCard(target);
        const message = errorMessage || "Data tidak dapat dimuat.";

        if (metricKeys.includes(target)) {
            card?.setAttribute("aria-busy", "false");
            const note = card?.querySelector("[data-sales-kpi-note]");
            const retry = card?.querySelector("[data-sales-retry]");
            if (note) note.textContent = message;
            if (retry) retry.hidden = false;
            return;
        }
        if (target === "metrics") {
            metricKeys.forEach((key) => {
                const card = findReportCard(key);
                card?.setAttribute("aria-busy", "false");
                const note = card?.querySelector("[data-sales-kpi-note]");
                const retry = card?.querySelector("[data-sales-retry]");
                if (note) note.textContent = message;
                if (retry) retry.hidden = false;
            });
            return;
        }

        if (!card) return;
        card.setAttribute("aria-busy", "false");

        if (target === "top-products") {
            state.products = [];
            renderProducts();
            const summary = root.querySelector("[data-product-list-summary]");
            if (summary) summary.textContent = message;
            root.querySelector('[data-sales-retry="top-products"]').hidden =
                false;
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
            root.querySelector("[data-transaction-loading]").classList.add(
                "hidden",
            );
            root.querySelector("[data-transaction-error-message]").textContent =
                message;
            root.querySelector("[data-transaction-error]").classList.remove(
                "hidden",
            );
        }
    });

    updateChartPeriod();
    renderProducts();
    renderTransactions();
    loadSummarySection("metrics", "metrics", state.metricPeriod);
    loadSummarySection("chart", "trend-chart", state.period);
    loadSummarySection("top-products", "top-products", state.period);
    loadTransactions();

    root.addEventListener("sales-report:period-change", () => {
        if (state.period === "custom") {
            const start = root.querySelector("[data-period-start]").value;
            const end = root.querySelector("[data-period-end]").value;
            if (!start || !end) return;
        }
        loadSummarySection("chart", "trend-chart", state.period);
        loadSummarySection("top-products", "top-products", state.period);
    });

    async function loadSummarySection(section, target, period) {
        const requestId = ++requests[target];
        const params = new URLSearchParams({ section, period });

        if (section === "metrics" && period === "week") {
            const range = rollingSevenDayRange();
            params.set("period", "custom");
            params.set("start_date", range.start);
            params.set("end_date", range.end);
        }

        if (metricKeys.includes(target)) {
            params.set("metric", metricApiKeys[target]);
        }

        if (period === "custom") {
            const start = root.querySelector("[data-period-start]").value;
            const end = root.querySelector("[data-period-end]").value;
            if (!start || !end) return;
            params.set("start_date", start);
            params.set("end_date", end);
        }

        root.dispatchEvent(
            new CustomEvent("sales-report:loading", {
                detail: { target, meta: { period } },
            }),
        );

        try {
            const { response, data } = await apiFetch(
                `/api/reports/sales/summary?${params}`,
            );
            if (requestId !== requests[target]) return;
            if (!response.ok || data.success !== true) {
                throw new Error(
                    data.message || "Data laporan tidak dapat dimuat.",
                );
            }

            const payload = data.data ?? {};
            const result =
                section === "metrics"
                    ? payload
                    : section === "chart"
                    ? (payload.chart ?? {})
                    : section === "top-products"
                      ? { products: payload.products ?? [] }
                      : (payload.metrics ?? {});
            root.dispatchEvent(
                new CustomEvent("sales-report:data", {
                    detail: {
                        target,
                        data: result,
                        meta: {
                            period,
                            periodRange: payload.period_range,
                        },
                    },
                }),
            );
        } catch (error) {
            if (
                requestId !== requests[target] ||
                error.message === "UNAUTHORIZED"
            )
                return;
            root.dispatchEvent(
                new CustomEvent("sales-report:error", {
                    detail: {
                        target,
                        message: apiErrorMessage(error),
                        meta: { period },
                    },
                }),
            );
        }
    }

    async function loadTransactions() {
        if (state.transactionLoading) return;

        const requestId = ++requests["transaction-history"];
        state.transactionLoading = true;
        const params = new URLSearchParams();
        Object.entries(state.appliedFilters).forEach(([key, value]) => {
            if (value) params.set(key, value);
        });
        params.set("per_page", String(state.pageSize));
        params.set("page", String(state.page));

        root.dispatchEvent(
            new CustomEvent("sales-report:loading", {
                detail: { target: "transaction-history" },
            }),
        );

        try {
            const { response, data } = await apiFetch(
                `/api/reports/sales?${params}`,
            );
            if (requestId !== requests["transaction-history"]) return;
            if (!response.ok || data.success !== true) {
                throw new Error(
                    data.message || "Riwayat transaksi tidak dapat dimuat.",
                );
            }
            state.transactionLoading = false;
            root.dispatchEvent(
                new CustomEvent("sales-report:data", {
                    detail: {
                        target: "transaction-history",
                        data: data.data ?? {},
                        meta: data.meta ?? {},
                    },
                }),
            );
        } catch (error) {
            if (requestId !== requests["transaction-history"]) return;
            if (error.message === "UNAUTHORIZED") return;
            root.dispatchEvent(
                new CustomEvent("sales-report:error", {
                    detail: {
                        target: "transaction-history",
                        message: apiErrorMessage(error),
                    },
                }),
            );
        } finally {
            if (requestId === requests["transaction-history"]) {
                state.transactionLoading = false;
                root.querySelector("[data-apply-filters]").disabled = false;
                root.querySelector("[data-reset-filters]").disabled = false;
                root.querySelector("[data-page-previous]").disabled =
                    state.meta.current_page <= 1;
                root.querySelector("[data-page-next]").disabled =
                    !state.meta.has_more;
            }
        }
    }

    async function loadTransactionDetail(orderId) {
        const requestId = ++requests.detail;
        const dialog = root.querySelector("[data-transaction-dialog]");
        root.querySelector("[data-detail-invoice]").textContent =
            "Memuat detail transaksi...";
        root.querySelector("[data-detail-error]").hidden = true;
        if (dialog && !dialog.open) dialog.showModal();

        try {
            const { response, data } = await apiFetch(
                `/api/reports/sales/${encodeURIComponent(orderId)}`,
            );
            if (requestId !== requests.detail) return;
            if (!response.ok || data.success !== true || !data.data) {
                throw new Error(
                    data.message || "Detail transaksi tidak dapat dimuat.",
                );
            }
            openTransactionDetail(data.data);
        } catch (error) {
            if (
                requestId !== requests.detail ||
                error.message === "UNAUTHORIZED"
            )
                return;
            root.querySelector("[data-detail-invoice]").textContent =
                apiErrorMessage(error);
            root.querySelector("[data-detail-items]").replaceChildren();
            const retry = root.querySelector("[data-detail-error]");
            retry.dataset.detailRetry = String(orderId);
            retry.hidden = false;
        }
    }

    function apiErrorMessage(error) {
        if (error.message === "NETWORK_ERROR")
            return "Koneksi gagal. Periksa jaringan lalu coba lagi.";
        return error.message || "Data tidak dapat dimuat. Silakan coba lagi.";
    }

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

    function readDraftFilters() {
        return {
            start_date: root.querySelector("[data-filter-start]").value,
            end_date: root.querySelector("[data-filter-end]").value,
            status: root.querySelector("[data-filter-status]").value,
            payment_status: root.querySelector("[data-filter-payment-status]")
                .value,
            payment_method: root.querySelector("[data-filter-payment]").value,
            cashier_id: root.querySelector("[data-filter-cashier]").value,
            search: root.querySelector("[data-filter-search]").value.trim(),
        };
    }

    function updateDraftFilterNotice() {
        const draft = readDraftFilters();
        const changed =
            state.draftPageSize !== state.pageSize ||
            Object.entries(draft).some(
                ([key, value]) => value !== state.appliedFilters[key],
            );
        root.querySelector("[data-filter-draft-notice]").hidden = !changed;
    }

    function rollingSevenDayRange() {
        const parts = new Intl.DateTimeFormat("en-US", {
            timeZone: "Asia/Jakarta",
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
        }).formatToParts(new Date());
        const values = Object.fromEntries(
            parts.map(({ type, value }) => [type, value]),
        );
        const end = `${values.year}-${values.month}-${values.day}`;
        const startDate = new Date(`${end}T00:00:00Z`);
        startDate.setUTCDate(startDate.getUTCDate() - 6);

        return {
            start: `${startDate.getUTCFullYear()}-${String(startDate.getUTCMonth() + 1).padStart(2, "0")}-${String(startDate.getUTCDate()).padStart(2, "0")}`,
            end,
        };
    }

    function renderMetric(key, metric, periodRange) {
        const card = findReportCard(key);
        const valueNode = root.querySelector(`[data-sales-kpi-value="${key}"]`);
        const noteNode = root.querySelector(`[data-sales-kpi-note="${key}"]`);
        const deltaNode = root.querySelector(`[data-sales-kpi-delta="${key}"]`);
        const periodNode = root.querySelector(
            `[data-sales-kpi-period="${key}"]`,
        );

        if (periodNode) periodNode.textContent = formatPeriodRange(periodRange);

        if (!metric || !Number.isFinite(Number(metric.current))) {
            card?.setAttribute("aria-busy", "false");
            if (valueNode) valueNode.textContent = "Tidak tersedia";
            if (noteNode)
                noteNode.textContent = "Ringkasan penjualan tidak tersedia.";
            return;
        }

        card?.setAttribute("aria-busy", "false");
        if (valueNode) {
            valueNode.textContent =
                key === "sales_count"
                    ? formatNumber(metric.current)
                    : formatRupiah(metric.current);
        }
        if (noteNode)
            noteNode.textContent = metric.note || "Pada periode terpilih";
        if (deltaNode) {
            if (
                metric.previous !== null &&
                metric.previous !== undefined &&
                Number.isFinite(Number(metric.previous))
            ) {
                const previous = Number(metric.previous);
                const current = Number(metric.current);
                const change = previous
                    ? ((current - previous) / previous) * 100
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
            } else {
                deltaNode.textContent = "Perbandingan belum tersedia";
                deltaNode.classList.remove(
                    "text-ckb-success",
                    "text-ckb-error",
                );
                deltaNode.classList.add("text-ckb-on-surface-variant");
            }
        }
    }

    function setMetricBusy(key, isBusy) {
        const card = findReportCard(key);
        card?.setAttribute("aria-busy", String(isBusy));
        if (isBusy) {
            const note = card?.querySelector("[data-sales-kpi-note]");
            if (note) note.textContent = "Memuat ringkasan...";
            const period = card?.querySelector("[data-sales-kpi-period]");
            if (period) period.textContent = metricPeriodLabel(state.metricPeriod);
        }
    }

    function metricPeriodLabel(period) {
        return {
            week: "7 Hari Terakhir",
            month: "Bulan Ini",
            year: "Tahun Ini",
        }[period] || "Periode tidak tersedia";
    }

    function formatPeriodRange(range) {
        if (!range?.start_date || !range?.end_date) {
            return "Periode tidak tersedia";
        }

        const formatDate = (date) =>
            new Intl.DateTimeFormat("id-ID", {
                day: "numeric",
                month: "short",
                year: "numeric",
                timeZone: "UTC",
            }).format(new Date(`${date}T00:00:00Z`));

        return `${formatDate(range.start_date)} – ${formatDate(range.end_date)}`;
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
        const pageItems = state.transactions;
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

            const paymentStatusNode = row.querySelector(
                '[data-transaction-field="payment_status"]',
            );
            if (paymentStatusNode) {
                paymentStatusNode.textContent = formatPaymentStatus(
                    transaction.payment_status,
                );
            }

            const statusNode = row.querySelector(
                '[data-transaction-field="status"]',
            );
            if (statusNode) {
                statusNode.textContent = formatOrderStatus(transaction.status);
                statusNode.classList.toggle(
                    "bg-ckb-success-container",
                    transaction.status === "completed",
                );
                statusNode.classList.toggle(
                    "text-ckb-on-success-container",
                    transaction.status === "completed",
                );
                statusNode.classList.toggle(
                    "bg-ckb-secondary-container",
                    transaction.status !== "completed",
                );
                statusNode.classList.toggle(
                    "text-ckb-on-secondary-container",
                    transaction.status !== "completed",
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
        const total = Number(state.meta.total ?? 0);
        const from = Number(state.meta.from ?? 0);
        const to = Number(state.meta.to ?? 0);

        if (summary) {
            summary.textContent = total
                ? `${formatNumber(total)} transaksi sesuai filter.`
                : "Tidak ada transaksi untuk filter ini.";
        }
        if (paginationStatus) {
            paginationStatus.textContent = total
                ? `Menampilkan ${formatNumber(from)}–${formatNumber(to)} dari ${formatNumber(total)} transaksi`
                : "0 transaksi";
        }
        if (previousButton)
            previousButton.disabled = state.meta.current_page <= 1;
        if (nextButton) nextButton.disabled = !state.meta.has_more;
        root.dataset.reportState = total ? "ready" : "empty";
    }

    function updateTransactionFilterOptions(filters) {
        replaceSelectOptions(
            "[data-filter-status]",
            "Semua status",
            filters.statuses ?? [],
            formatOrderStatus,
        );
        replaceSelectOptions(
            "[data-filter-payment-status]",
            "Semua status pembayaran",
            filters.payment_statuses ?? [],
            formatPaymentStatus,
        );
        replaceSelectOptions(
            "[data-filter-payment]",
            "Semua metode",
            filters.payment_methods ?? [],
            formatPaymentMethod,
        );

        const select = root.querySelector("[data-filter-cashier]");
        if (!select) return;
        const selected = select.value;
        select.replaceChildren(new Option("Semua kasir", ""));
        (filters.cashiers ?? []).forEach((cashier) => {
            select.add(new Option(cashier.name || "Kasir", String(cashier.id)));
        });
        select.value = [...select.options].some(
            (option) => option.value === selected,
        )
            ? selected
            : "";
    }

    function replaceSelectOptions(selector, firstLabel, values, formatLabel) {
        const select = root.querySelector(selector);
        if (!select) return;
        const selected = select.value;
        select.replaceChildren(new Option(firstLabel, ""));
        values.forEach((value) =>
            select.add(new Option(formatLabel(value), value)),
        );
        select.value = values.includes(selected) ? selected : "";
    }

    function openTransactionDetail(transaction) {
        const dialog = root.querySelector("[data-transaction-dialog]");
        const fields = {
            transaction_time: formatDateTime(transaction.transaction_time),
            cashier:
                transaction.cashier?.name || transaction.cashier_name || "—",
            order_type: transaction.order_type || "—",
            payment_method: formatPaymentMethod(transaction.payment_method),
            status: formatOrderStatus(transaction.status),
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
                    name: [
                        item.product_name_snapshot ||
                            item.product_name ||
                            "Nama produk tidak tersedia",
                        item.variant_summary_snapshot,
                        (item.options ?? [])
                            .map(
                                (option) =>
                                    `${option.option_group_name_snapshot}: ${option.option_name_snapshot}`,
                            )
                            .join(", "),
                    ]
                        .filter(Boolean)
                        .join(" · "),
                    quantity: formatNumber(item.quantity),
                    unit_price: formatRupiah(
                        Number(item.unit_price || 0) +
                            Number(item.option_price || 0),
                    ),
                    subtotal: formatRupiah(
                        item.final_subtotal ?? item.subtotal,
                    ),
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

        if (dialog && !dialog.open) dialog.showModal();
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
                  timeZone: "Asia/Jakarta",
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
        return (
            {
                paid: "Lunas",
                unpaid: "Belum dibayar",
                cancelled: "Dibatalkan",
            }[value] ||
            value ||
            "—"
        );
    }

    function formatOrderStatus(value) {
        return (
            {
                completed: "Selesai",
                cancelled: "Dibatalkan",
            }[value] ||
            value ||
            "—"
        );
    }
}
