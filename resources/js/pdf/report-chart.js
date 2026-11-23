(async function renderPdfChart() {
    window.pdfReady = false;

    try {
        await Promise.all([
            document.fonts?.ready ?? Promise.resolve(),
            ...[...document.images].map((image) =>
                image.complete
                    ? Promise.resolve()
                    : new Promise((resolve, reject) => {
                          image.addEventListener("load", resolve, { once: true });
                          image.addEventListener(
                              "error",
                              () => reject(new Error("PDF image failed to load")),
                              { once: true },
                          );
                      }),
            ),
        ]);

        if (typeof Chart !== "function") {
            throw new Error("Chart.js did not load for the report PDF");
        }

        const canvas = document.querySelector("[data-pdf-chart]");
        const configuration = window.pdfChartConfiguration;
        if (!canvas || !configuration) {
            throw new Error("The report chart configuration is missing");
        }

        const formatCurrency = (value) =>
            `Rp ${new Intl.NumberFormat("id-ID", {
                notation: "compact",
                maximumFractionDigits: 1,
            }).format(Number(value) || 0)}`;
        const formatTransactions = (value) =>
            new Intl.NumberFormat("id-ID").format(Number(value) || 0);
        const currencyChart = configuration.metric !== "transactions";

        new Chart(canvas, {
            type: configuration.type,
            data: {
                labels: configuration.labels,
                datasets: [
                    {
                        label: configuration.label,
                        data: configuration.values,
                        borderColor: "#142747",
                        backgroundColor: "rgba(73, 107, 154, 0.14)",
                        pointBackgroundColor: "#ffffff",
                        pointBorderColor: "#142747",
                        borderWidth: 2,
                        pointRadius: 2.5,
                        tension: 0.28,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 0,
                    onComplete: () => {
                        window.pdfReady = true;
                    },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: "#596579",
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 12,
                            font: { size: 9 },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: "#e2ded2" },
                        ticks: {
                            color: "#596579",
                            maxTicksLimit: 5,
                            callback: currencyChart
                                ? formatCurrency
                                : formatTransactions,
                            font: { size: 9 },
                        },
                    },
                },
            },
        });
    } catch (error) {
        window.pdfError = error instanceof Error ? error.message : String(error);
        console.error("Report PDF chart rendering failed", error);
    }
})();
