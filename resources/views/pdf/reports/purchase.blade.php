<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reportTitle }}</title>
    <style>{!! $pdfStyles !!}</style>
</head>
<body>
    <main class="report-document">
        <header class="report-heading">
            <p class="report-brand">Warkop Cak Kebo · Laporan Administrasi</p>
            <h1>Laporan Pembelian</h1>
            <p class="report-period">Periode: {{ $reportingPeriod }}</p>
            <p class="report-generated">Dibuat {{ $generatedAt->format('d-m-Y H:i') }}</p>
        </header>

        <section aria-label="Ringkasan pembelian">
            <div class="summary-grid">
                <article class="summary-item">
                    <p class="summary-label">Total pembelian</p>
                    <p class="summary-value">Rp {{ number_format($metrics['total_purchases'], 0, ',', '.') }}</p>
                    <p class="summary-period">Semua status pada periode ini</p>
                </article>
                <article class="summary-item">
                    <p class="summary-label">Invoice pembelian</p>
                    <p class="summary-value">{{ number_format($metrics['invoice_count'], 0, ',', '.') }}</p>
                    <p class="summary-period">{{ number_format($metrics['received_count'], 0, ',', '.') }} invoice diterima</p>
                </article>
                <article class="summary-item">
                    <p class="summary-label">Rata-rata pembelian</p>
                    <p class="summary-value">Rp {{ number_format($metrics['average_purchase'], 0, ',', '.') }}</p>
                    <p class="summary-period">Per invoice pada periode ini</p>
                </article>
            </div>
        </section>

        @if ($topSuppliers->isNotEmpty())
            <section class="report-section" aria-labelledby="purchase-suppliers-title">
                <h2 id="purchase-suppliers-title">Supplier teratas</h2>
                <table class="payment-summary">
                    <thead>
                        <tr>
                            <th scope="col">Supplier</th>
                            <th scope="col" class="number">Invoice</th>
                            <th scope="col" class="number">Total pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topSuppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->supplier_name }}</td>
                                <td class="number">{{ number_format($supplier->invoice_count, 0, ',', '.') }}</td>
                                <td class="number">Rp {{ number_format($supplier->total_cost, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

        <section class="report-section" aria-labelledby="purchase-chart-title">
            <h2 id="purchase-chart-title">Tren pengeluaran · {{ $chartPeriodLabel }}</h2>
            <div class="chart-container">
                <canvas data-pdf-chart width="1000" height="370" role="img" aria-label="Grafik tren pengeluaran pembelian"></canvas>
            </div>
        </section>

        <section class="report-section" aria-labelledby="purchase-history-title">
            <h2 id="purchase-history-title">Riwayat pembelian</h2>
            <p class="muted">Invoice semua status pada periode terpilih.</p>
            <table class="report-table">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="number">Item</th>
                        <th scope="col" class="number">Total pembelian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</td>
                            <td>{{ $purchase->invoice_number }}</td>
                            <td>{{ $purchase->supplier_name }}</td>
                            <td class="status">{{ $purchase->status === 'received' ? 'Diterima' : 'Menunggu' }}</td>
                            <td class="number">{{ number_format($purchase->items_count, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row">Tidak ada invoice pembelian pada periode ini.</td></tr>
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
