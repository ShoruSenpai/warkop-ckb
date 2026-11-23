import { apiFetch } from "@/utils/api.js";
import { Chart, ckbChartColors } from "@/utils/chart.js";
import { downloadReportPdf } from "@/utils/report-download.js";

const root = document.querySelector("[data-purchase-overview]");

if (root) {
    const state = { period: "month", chartPeriod: "month", pdfExporting: false };
    let spendingChart;
    let overviewRequest = 0;
    let metricsRequest = 0;
    let chartRequest = 0;
    const metricValues = new Map(
        [...root.querySelectorAll("[data-report-value]")].map((node) => [
            node.dataset.reportValue,
            node,
        ]),
    );
    const metricNotes = new Map(
        [...root.querySelectorAll("[data-report-note]")].map((node) => [
            node.dataset.reportNote,
            node,
        ]),
    );

    root.addEventListener("click", (event) => {
        const exportButton = event.target.closest("[data-purchase-export]");
        if (exportButton && !state.pdfExporting) {
            state.pdfExporting = true;
            const query = new URLSearchParams({
                period: state.period,
                chart_period: state.chartPeriod,
            });

            downloadReportPdf(
                exportButton,
                `/reports/purchases/pdf?${query}`,
                "pembelian",
            ).finally(() => {
                state.pdfExporting = false;
            });
            return;
        }

        const control = event.target.closest("[data-filter-group]");

        if (!control) return;

        const group = control.dataset.filterGroup;

        if (group === "purchase-metrics-period") {
            state.period = control.dataset.filterValue;
        } else if (group === "purchase-chart-period") {
            state.chartPeriod = control.dataset.filterValue;
        } else {
            return;
        }

        setPeriodFilterState(group, control);

        if (group === "purchase-metrics-period") loadMetrics();
        if (group === "purchase-chart-period") loadChart();
    });

    loadOverview();
    loadChart();

    async function loadOverview() {
        const requestId = ++overviewRequest;
        const requestedPeriod = state.period;
        setMetricsBusy(true);

        try {
            const query = new URLSearchParams({
                section: "overview",
                period: state.period,
            });
            const { response, data } = await apiFetch(
                `/api/reports/purchases/summary?${query}`,
            );

            if (!response.ok || !data?.success) {
                throw new Error(
                    data?.message || "Ringkasan pembelian tidak dapat dimuat.",
                );
            }

            if (requestId !== overviewRequest) return;

            if (state.period === requestedPeriod) {
                renderMetrics(data.data?.metrics ?? {});
            }
            renderSuppliers(data.data?.top_suppliers ?? []);
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;
            if (requestId !== overviewRequest) return;

            if (requestedPeriod === state.period) {
                renderMetricsError(error.message);
            }
            renderSuppliersError(error.message);
        } finally {
            if (
                requestId === overviewRequest &&
                requestedPeriod === state.period &&
                metricsRequest === 0
            ) {
                setMetricsBusy(false);
            }
        }
    }

    async function loadMetrics() {
        const requestId = ++metricsRequest;
        setMetricsBusy(true);

        try {
            const query = new URLSearchParams({
                section: "metrics",
                period: state.period,
            });
            const { response, data } = await apiFetch(
                `/api/reports/purchases/summary?${query}`,
            );

            if (!response.ok || !data?.success) {
                throw new Error(
                    data?.message || "Metrik pembelian tidak dapat dimuat.",
                );
            }

            if (requestId !== metricsRequest) return;

            renderMetrics(data.data?.metrics ?? {});
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;
            if (requestId !== metricsRequest) return;

            renderMetricsError(error.message);
        } finally {
            if (requestId === metricsRequest) setMetricsBusy(false);
        }
    }

    async function loadChart() {
        const requestId = ++chartRequest;
        const chart = root.querySelector("[data-spending-chart]");
        const chartState = root.querySelector("[data-chart-state]");

        chart?.setAttribute("aria-busy", "true");
        if (chartState) chartState.textContent = "Memuat tren pengeluaran...";

        try {
            const query = new URLSearchParams({
                section: "chart",
                chart_period: state.chartPeriod,
            });
            const { response, data } = await apiFetch(
                `/api/reports/purchases/summary?${query}`,
            );

            if (!response.ok || !data?.success) {
                throw new Error(
                    data?.message || "Grafik pengeluaran tidak dapat dimuat.",
                );
            }

            if (requestId !== chartRequest) return;

            renderChart(data.data?.chart ?? {});
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;
            if (requestId !== chartRequest) return;

            renderChartError(error.message);
        } finally {
            if (requestId === chartRequest) {
                chart?.setAttribute("aria-busy", "false");
            }
        }
    }

    function setPeriodFilterState(group, selectedControl) {
        root
            .querySelectorAll(`[data-filter-group="${group}"]`)
            .forEach((button) => {
                const isSelected = button === selectedControl;
                button.setAttribute("aria-pressed", String(isSelected));
                button.classList.toggle("bg-ckb-primary", isSelected);
                button.classList.toggle("text-ckb-on-primary", isSelected);
                button.classList.toggle("shadow-surface", isSelected);
                button.classList.toggle(
                    "text-ckb-on-secondary-container",
                    !isSelected,
                );
            });
    }

    function setMetricsBusy(isBusy) {
        root
            .querySelector('[data-purchase-report-card="overview-metrics"]')
            ?.setAttribute("aria-busy", String(isBusy));
    }

    function renderMetrics(metrics) {
        setMetric("total_purchases", formatRupiah(metrics.total_purchases));
        setMetric("invoice_count", formatNumber(metrics.invoice_count));
        setMetric("average_purchase", formatRupiah(metrics.average_purchase));

        const receivedNote = metricNotes.get("invoice_count");
        if (receivedNote) {
            receivedNote.textContent = `${formatNumber(metrics.received_count)} invoice diterima pada periode ini`;
        }
    }

    function setMetric(key, value) {
        const node = metricValues.get(key);
        if (node) node.textContent = value;
    }

    function renderSuppliers(suppliers) {
        const list = root.querySelector("[data-top-suppliers]");
        if (!list) return;

        list.replaceChildren();

        if (!suppliers.length) {
            list.append(
                makeMessage("Belum ada pembelian supplier pada periode ini."),
            );
            return;
        }

        suppliers.forEach((supplier, index) => {
            const row = document.createElement("li");
            row.className = "flex items-center gap-3 py-2.5";

            const rank = document.createElement("span");
            rank.className =
                "flex size-8 shrink-0 items-center justify-center rounded-lg bg-ckb-secondary-container text-xs font-bold text-ckb-on-secondary-container";
            rank.textContent = String(index + 1);

            const details = document.createElement("div");
            details.className = "min-w-0 flex-1";

            const name = document.createElement("p");
            name.className = "truncate text-xs font-semibold text-ckb-primary";
            name.textContent =
                supplier.supplier_name || "Nama supplier tidak tersedia";

            const invoiceCount = document.createElement("p");
            invoiceCount.className =
                "mt-1 text-[10px] text-ckb-on-surface-variant";
            invoiceCount.textContent = `${formatNumber(supplier.invoice_count)} invoice`;

            const total = document.createElement("p");
            total.className =
                "shrink-0 text-right text-xs font-semibold text-ckb-primary";
            total.textContent = formatRupiah(supplier.total_cost);

            details.append(name, invoiceCount);
            row.append(rank, details, total);
            list.append(row);
        });
    }

    function renderChart(chartData) {
        const chart = root.querySelector("[data-spending-chart]");
        const canvas = root.querySelector("[data-spending-chart-canvas]");
        const chartState = root.querySelector("[data-chart-state]");
        const values = Array.isArray(chartData.values) ? chartData.values : [];
        const labels = Array.isArray(chartData.labels) ? chartData.labels : [];

        if (!canvas || !values.length || values.length !== labels.length) {
            if (spendingChart) {
                spendingChart.data.labels = [];
                spendingChart.data.datasets[0].data = [];
                spendingChart.update();
            }
            if (chartState)
                chartState.textContent =
                    "Belum ada data pengeluaran pada periode ini.";
            return;
        }

        const colors = ckbChartColors();
        const numericValues = values.map((value) => Number(value) || 0);
        const chartDataSet = {
            label: "Pengeluaran pembelian",
            data: numericValues,
            borderColor: colors.primary,
            backgroundColor: `${colors.accent}22`,
            pointBackgroundColor: colors.surface,
            pointBorderColor: colors.primary,
            pointHoverBackgroundColor: colors.primary,
            pointHoverBorderColor: colors.surface,
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 5,
            fill: true,
            tension: 0.32,
            spanGaps: true,
        };

        if (!spendingChart) {
            spendingChart = new Chart(canvas, {
                type: "line",
                data: { labels, datasets: [chartDataSet] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: "index", intersect: false },
                    animation: window.matchMedia(
                        "(prefers-reduced-motion: reduce)",
                    ).matches
                        ? false
                        : { duration: 250 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) =>
                                    ` ${formatRupiah(context.parsed.y)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: colors.muted,
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 6,
                                font: { size: 10 },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: colors.grid },
                            ticks: {
                                color: colors.muted,
                                padding: 8,
                                maxTicksLimit: 5,
                                callback: formatCompactRupiah,
                                font: { size: 10 },
                            },
                        },
                    },
                },
            });
        } else {
            spendingChart.data.labels = labels;
            spendingChart.data.datasets[0] = chartDataSet;
            spendingChart.options.scales.x.ticks.color = colors.muted;
            spendingChart.options.scales.y.ticks.color = colors.muted;
            spendingChart.options.scales.y.grid.color = colors.grid;
            spendingChart.update();
        }

        const hasData = numericValues.some((value) => value > 0);
        if (chartState) {
            chartState.textContent = hasData
                ? `Total grafik: ${formatRupiah(numericValues.reduce((sum, value) => sum + value, 0))}`
                : "Belum ada data pengeluaran pada periode ini.";
        }
        chart?.setAttribute("aria-busy", "false");
    }

    function renderMetricsError(message) {
        setMetric("total_purchases", "Tidak tersedia");
        setMetric("invoice_count", "Tidak tersedia");
        setMetric("average_purchase", "Tidak tersedia");
        const receivedNote = metricNotes.get("invoice_count");
        if (receivedNote) receivedNote.textContent = message;
    }

    function renderSuppliersError(message) {
        const list = root.querySelector("[data-top-suppliers]");
        list?.replaceChildren(
            makeMessage(message || "Supplier teratas tidak dapat dimuat."),
        );
    }

    function renderChartError(message) {
        const chartState = root.querySelector("[data-chart-state]");
        root
            .querySelector("[data-spending-chart]")
            ?.setAttribute("data-chart-state", "error");
        if (spendingChart) {
            spendingChart.data.labels = [];
            spendingChart.data.datasets[0].data = [];
            spendingChart.update();
        }
        if (chartState) {
            chartState.textContent = message || "Grafik tidak dapat dimuat.";
        }
    }

    function makeMessage(message) {
        const row = document.createElement("li");
        row.className = "py-6 text-center text-xs text-ckb-on-surface-variant";
        row.textContent = message;
        return row;
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

    function formatCompactRupiah(value) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            notation: "compact",
            maximumFractionDigits: 1,
        }).format(Number(value) || 0);
    }
}
