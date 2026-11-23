<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reportTitle }}</title>
    <style>{!! $pdfStyles !!}</style>
</head>
<body class="sales-report">
    <main class="report-document">
        <header class="report-heading">
            <p class="report-brand">Warkop Cak Kebo · Laporan Administrasi</p>
            <h1>Laporan Penjualan</h1>
            <p class="report-period">Grafik: {{ $chartPeriodLabel }}</p>
            <p class="report-generated">Dibuat {{ $generatedAt->format('d-m-Y H:i') }}</p>
        </header>

        <section aria-label="Ringkasan penjualan">
            <div class="summary-grid">
                <article class="summary-item">
                    <p class="summary-label">Total penjualan</p>
                    <p class="summary-value">Rp {{ number_format($metrics['sales_total']['value'], 0, ',', '.') }}</p>
                    <p class="summary-period">{{ $metrics['sales_total']['range'] }}</p>
                </article>
                <article class="summary-item">
                    <p class="summary-label">Transaksi selesai dan dibayar</p>
                    <p class="summary-value">{{ number_format($metrics['sales_count']['value'], 0, ',', '.') }}</p>
                    <p class="summary-period">{{ $metrics['sales_count']['range'] }}</p>
                </article>
                <article class="summary-item">
                    <p class="summary-label">Rata-rata nota</p>
                    <p class="summary-value">Rp {{ number_format($metrics['sales_average']['value'], 0, ',', '.') }}</p>
                    <p class="summary-period">{{ $metrics['sales_average']['range'] }}</p>
                </article>
            </div>
        </section>

        <section class="report-section" aria-labelledby="sales-payment-title">
            <h2 id="sales-payment-title">Ringkasan metode pembayaran</h2>
            <p class="muted">Order selesai dan dibayar · {{ $chartPeriodLabel }}</p>
            <table class="payment-summary">
                <thead>
                    <tr>
                        <th scope="col">Metode pembayaran</th>
                        <th scope="col" class="number">Transaksi</th>
                        <th scope="col" class="number">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment['label'] }}</td>
                            <td class="number">{{ number_format($payment['count'], 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($payment['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty-row">Tidak ada transaksi selesai dan dibayar pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="report-section" aria-labelledby="sales-chart-title">
            <h2 id="sales-chart-title">{{ $chartMetric === 'transactions' ? 'Tren jumlah transaksi' : 'Tren omzet' }}</h2>
            <div class="chart-container">
                <canvas data-pdf-chart width="1000" height="370" role="img" aria-label="Grafik penjualan"></canvas>
            </div>
        </section>

        <section class="report-section" aria-labelledby="sales-history-title">
            <h2 id="sales-history-title">Riwayat penjualan</h2>
            <p class="muted">{{ $historyFilterLabel }}</p>
            <table class="report-table">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Kasir</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Pembayaran</th>
                        <th scope="col" class="number">Total</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction['transaction_time'] }}</td>
                            <td>{{ $transaction['invoice_code'] }}</td>
                            <td>{{ $transaction['cashier_name'] ?: '—' }}</td>
                            <td>{{ $transaction['order_type'] ?: '—' }}</td>
                            <td>{{ $transaction['payment_method_label'] }}</td>
                            <td class="number">Rp {{ number_format($transaction['final_amount'], 0, ',', '.') }}</td>
                            <td class="status">{{ $transaction['status_label'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-row">Tidak ada transaksi sesuai filter riwayat yang diterapkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <script>{!! $chartJs !!}</script>
    <script>
        window.pdfChartConfiguration = {{ Illuminate\Support\Js::from($chartConfiguration) }};
    </script>
    <script>{!! $chartRenderer !!}</script>
</body>
</html>
