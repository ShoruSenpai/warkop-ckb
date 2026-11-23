<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Reports\PdfReportRenderer;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function show(Order $order)
    {
        $order->load([
            "items" => fn ($query) => $query->orderBy("id"),
            "items.options" => fn ($query) => $query->orderBy("id"),
            "items.discounts" => fn ($query) => $query->orderBy("id"),
            "discounts" => fn ($query) => $query->orderBy("id"),
        ]);

        $timezone = config("business.timezone", "Asia/Jakarta");
        $data = [
            "id" => $order->id,
            "invoice_code" => $order->invoice_code,
            "transaction_time" => $order->transaction_time?->setTimezone($timezone)->toIso8601String(),
            "paid_at" => $order->paid_at?->setTimezone($timezone)->toIso8601String(),
            "receipt_issued_at" => $order->receipt_issued_at?->setTimezone($timezone)->toIso8601String(),
            "cashier_id" => $order->cashier_id,
            "cashier_name" => $order->cashier_name_snapshot,
            "store_name" => $order->store_name_snapshot,
            "status" => $order->status,
            "order_type" => $order->order_type,
            "payment_method" => $order->payment_method,
            "payment_status" => $order->payment_status,
            "gross_amount" => $order->gross_amount,
            "discount_amount" => $order->discount_amount,
            "final_amount" => $order->final_amount,
            "items" => $order->items->map(fn (OrderItem $item) => [
                "id" => $item->id,
                "product_id" => $item->product_id,
                "product_name" => $item->product_name_snapshot,
                "product_name_snapshot" => $item->product_name_snapshot,
                "variant_summary_snapshot" => $item->variant_summary_snapshot,
                "quantity" => $item->quantity,
                "unit_price" => $item->unit_price,
                "option_price" => $item->option_price,
                "subtotal" => $item->subtotal,
                "discount_amount" => $item->discount_amount,
                "final_subtotal" => $item->final_subtotal,
                "options" => $item->options->map(fn ($option) => [
                    "option_group_name_snapshot" => $option->option_group_name_snapshot,
                    "option_name_snapshot" => $option->option_name_snapshot,
                    "extra_price_snapshot" => $option->extra_price_snapshot,
                ])->values(),
                "discounts" => $item->discounts->map(fn ($discount) => [
                    "discount_name_snapshot" => $discount->discount_name_snapshot,
                    "discount_type_snapshot" => $discount->discount_type_snapshot,
                    "discount_value_snapshot" => $discount->discount_value_snapshot,
                    "discount_amount" => $discount->discount_amount,
                ])->values(),
            ])->values(),
            "discounts" => $order->discounts->map(fn ($discount) => [
                "discount_name_snapshot" => $discount->discount_name_snapshot,
                "discount_type_snapshot" => $discount->discount_type_snapshot,
                "discount_value_snapshot" => $discount->discount_value_snapshot,
                "discount_amount" => $discount->discount_amount,
            ])->values(),
        ];

        return response()->json([
            "success" => true,
            "message" => "Get Sales Transaction Detail",
            "data" => $data,
        ]);
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            "start_date" => "sometimes|nullable|date_format:Y-m-d",
            "end_date" => "sometimes|nullable|date_format:Y-m-d|after_or_equal:start_date",
            "status" => "sometimes|nullable|string|max:20",
            "payment_status" => "sometimes|nullable|string|max:20",
            "payment_method" => "sometimes|nullable|string|max:20",
            "cashier_id" => "sometimes|nullable|integer|min:1",
            "search" => "sometimes|string|max:100",
            "per_page" => "sometimes|integer|min:1|max:50",
            "page" => "sometimes|integer|min:1",
        ]);

        $timezone = config("business.timezone", "Asia/Jakarta");
        $orders = $this->transactionQuery($filters)
            ->paginate($filters["per_page"] ?? 10, ["*"], "page", $filters["page"] ?? 1);

        $transactions = $orders->getCollection()
            ->map(fn (Order $order): array => $this->transactionData($order, $timezone))
            ->all();

        $filterOptions = [
            "statuses" => DB::table("orders")
                ->distinct()
                ->orderBy("status")
                ->pluck("status")
                ->values(),
            "payment_statuses" => DB::table("orders")
                ->distinct()
                ->orderBy("payment_status")
                ->pluck("payment_status")
                ->values(),
            "payment_methods" => DB::table("orders")
                ->whereNotNull("payment_method")
                ->distinct()
                ->orderBy("payment_method")
                ->pluck("payment_method")
                ->values(),
            "cashiers" => DB::table("orders")
                ->whereNotNull("cashier_id")
                ->selectRaw("cashier_id AS id, MAX(cashier_name_snapshot) AS name")
                ->groupBy("cashier_id")
                ->orderBy("name")
                ->get(),
        ];

        return response()->json([
            "success" => true,
            "message" => "Get Sales Transactions",
            "data" => ["transactions" => $transactions],
            "meta" => [
                "current_page" => $orders->currentPage(),
                "per_page" => $orders->perPage(),
                "total" => $orders->total(),
                "last_page" => $orders->lastPage(),
                "from" => $orders->firstItem(),
                "to" => $orders->lastItem(),
                "has_more" => $orders->hasMorePages(),
                "filters" => $filterOptions,
            ],
        ]);
    }

    public function summary(Request $request)
    {
        $filters = $request->validate([
            "period" => "sometimes|in:today,week,month,year,custom",
            "start_date" => "required_if:period,custom|date_format:Y-m-d",
            "end_date" => "required_if:period,custom|date_format:Y-m-d|after_or_equal:start_date",
            "section" => "sometimes|in:all,overview,metrics,chart,top-products",
            "metric" => "sometimes|in:total_sales,transaction_count,average_ticket",
        ]);

        $period = $filters["period"] ?? "month";
        $section = $filters["section"] ?? "all";
        [$from, $until] = $this->periodRange($period, $filters);
        $data = [];

        if (in_array($section, ["all", "overview", "metrics"], true)) {
            $data["period"] = $period;
            $data["period_range"] = [
                "start_date" => $from->toDateString(),
                "end_date" => $until->subDay()->toDateString(),
            ];
            $data["metrics"] = $this->salesMetrics($from, $until, $filters["metric"] ?? null);
        }

        if (in_array($section, ["all", "chart"], true)) {
            $data["chart"] = $this->salesChart($period, $from, $until);
        }

        if (in_array($section, ["all", "overview", "top-products"], true)) {
            $data["products"] = $this->topProducts($from, $until);
        }

        return response()->json([
            "success" => true,
            "message" => "Get Sales Report Summary",
            "data" => $data,
        ]);
    }

    public function pdf(Request $request, PdfReportRenderer $renderer)
    {
        $filters = $request->validate([
            "sales_total_period" => "required|in:week,month,year",
            "sales_count_period" => "required|in:week,month,year",
            "sales_average_period" => "required|in:week,month,year",
            "chart_period" => "required|in:today,week,month,year,custom",
            "chart_start_date" => "required_if:chart_period,custom|date_format:Y-m-d",
            "chart_end_date" => "required_if:chart_period,custom|date_format:Y-m-d|after_or_equal:chart_start_date",
            "chart_metric" => "sometimes|in:revenue,transactions",
            "start_date" => "sometimes|nullable|date_format:Y-m-d",
            "end_date" => "sometimes|nullable|date_format:Y-m-d|after_or_equal:start_date",
            "status" => "sometimes|nullable|string|max:20",
            "payment_status" => "sometimes|nullable|string|max:20",
            "payment_method" => "sometimes|nullable|string|max:20",
            "cashier_id" => "sometimes|nullable|integer|min:1",
            "search" => "sometimes|nullable|string|max:100",
        ]);

        $metricPeriods = [
            "sales_total" => $filters["sales_total_period"],
            "sales_count" => $filters["sales_count_period"],
            "sales_average" => $filters["sales_average_period"],
        ];
        $metricNames = [
            "sales_total" => "total_sales",
            "sales_count" => "transaction_count",
            "sales_average" => "average_ticket",
        ];
        $metrics = [];

        foreach ($metricPeriods as $key => $metricPeriod) {
            [$from, $until] = $this->metricPeriodRange($metricPeriod);
            $metric = $this->salesMetrics($from, $until, $metricNames[$key])[$metricNames[$key]];
            $metrics[$key] = [
                "value" => $metric["current"],
                "range" => $this->formatPeriodRange($from, $until),
            ];
        }

        $chartPeriod = $filters["chart_period"];
        $chartFilters = $chartPeriod === "custom"
            ? ["start_date" => $filters["chart_start_date"], "end_date" => $filters["chart_end_date"]]
            : [];
        [$chartFrom, $chartUntil] = $this->periodRange($chartPeriod, $chartFilters);
        $chart = $this->salesChart($chartPeriod, $chartFrom, $chartUntil);
        $chartMetric = $filters["chart_metric"] ?? "revenue";
        $timezone = config("business.timezone", "Asia/Jakarta");
        $tableFilters = array_intersect_key($filters, array_flip([
            "start_date",
            "end_date",
            "status",
            "payment_status",
            "payment_method",
            "cashier_id",
            "search",
        ]));
        $transactions = $this->transactionQuery($tableFilters)
            ->get()
            ->map(fn (Order $order): array => [
                ...$this->transactionData($order, $timezone),
                "transaction_time" => $order->transaction_time?->setTimezone($timezone)->format("d-m-Y H:i") ?? "—",
                "payment_method_label" => $this->paymentMethodLabel($order->payment_method),
                "status_label" => $this->orderStatusLabel($order->status),
            ]);
        $payments = $this->eligibleOrders($chartFrom, $chartUntil)
            ->selectRaw("payment_method, COUNT(*) AS transaction_count, SUM(final_amount) AS total")
            ->groupBy("payment_method")
            ->orderBy("payment_method")
            ->get()
            ->map(fn ($payment): array => [
                "label" => $this->paymentMethodLabel($payment->payment_method),
                "count" => (int) $payment->transaction_count,
                "total" => (int) $payment->total,
            ]);

        return $renderer->download("pdf.reports.sales", [
            "reportTitle" => "Laporan Penjualan",
            "metrics" => $metrics,
            "payments" => $payments,
            "chart" => $chart,
            "chartMetric" => $chartMetric,
            "chartPeriodLabel" => $this->formatPeriodRange($chartFrom, $chartUntil),
            "chartConfiguration" => [
                "type" => "line",
                "label" => $chartMetric === "transactions" ? "Transaksi" : "Omzet",
                "metric" => $chartMetric,
                "labels" => $chart["labels"],
                "values" => $chartMetric === "transactions" ? $chart["transactions"] : $chart["revenue"],
            ],
            "transactions" => $transactions,
            "historyFilterLabel" => $this->historyFilterLabel($tableFilters),
        ], "laporan-penjualan", landscape: true);
    }

    private function salesMetrics(CarbonImmutable $from, CarbonImmutable $until, ?string $metric = null): array
    {
        if ($metric === "total_sales") {
            return ["total_sales" => [
                "current" => (int) $this->eligibleOrders($from, $until)->sum("final_amount"),
                "note" => "Penjualan selesai dan dibayar",
            ]];
        }

        if ($metric === "transaction_count") {
            return ["transaction_count" => [
                "current" => $this->eligibleOrders($from, $until)->count(),
                "note" => "Order selesai dan dibayar",
            ]];
        }

        $totals = $this->eligibleOrders($from, $until)
            ->selectRaw("COALESCE(SUM(final_amount), 0) AS total_sales, COUNT(*) AS transaction_count")
            ->first();
        $totalSales = (int) $totals->total_sales;
        $transactionCount = (int) $totals->transaction_count;
        $average = [
            "current" => $transactionCount ? (int) round($totalSales / $transactionCount) : 0,
            "note" => "Rata-rata nilai order dibayar",
        ];

        if ($metric === "average_ticket") {
            return ["average_ticket" => $average];
        }

        return [
            "total_sales" => ["current" => $totalSales, "note" => "Pada periode terpilih"],
            "transaction_count" => ["current" => $transactionCount, "note" => "Order selesai dan dibayar"],
            "average_ticket" => $average,
        ];
    }

    private function metricPeriodRange(string $period): array
    {
        if ($period !== "week") {
            return $this->periodRange($period, []);
        }

        $today = CarbonImmutable::today(config("business.timezone", "Asia/Jakarta"));

        return [$today->subDays(6), $today->addDay()];
    }

    private function formatPeriodRange(CarbonImmutable $from, CarbonImmutable $until): string
    {
        return $from->locale("id")->translatedFormat("j M Y")
            ." – "
            .$until->subDay()->locale("id")->translatedFormat("j M Y");
    }

    private function transactionQuery(array $filters): Builder
    {
        $query = Order::query()->select([
            "id",
            "invoice_code",
            "transaction_time",
            "cashier_id",
            "cashier_name_snapshot",
            "status",
            "order_type",
            "payment_method",
            "payment_status",
            "gross_amount",
            "discount_amount",
            "final_amount",
        ]);
        $timezone = config("business.timezone", "Asia/Jakarta");

        if (! empty($filters["start_date"])) {
            $from = CarbonImmutable::createFromFormat("!Y-m-d", $filters["start_date"], $timezone);
            $query->where("transaction_time", ">=", $this->databaseBoundary($from));
        }
        if (! empty($filters["end_date"])) {
            $until = CarbonImmutable::createFromFormat("!Y-m-d", $filters["end_date"], $timezone)->addDay();
            $query->where("transaction_time", "<", $this->databaseBoundary($until));
        }

        foreach (["status", "payment_status", "payment_method", "cashier_id"] as $filter) {
            if (isset($filters[$filter]) && $filters[$filter] !== "") {
                $query->where($filter, $filters[$filter]);
            }
        }

        if (! empty($filters["search"])) {
            $term = "%".mb_strtolower(trim($filters["search"]))."%";
            $query->where(function (Builder $query) use ($term): void {
                $query
                    ->whereRaw("LOWER(invoice_code) LIKE ?", [$term])
                    ->orWhereRaw("LOWER(cashier_name_snapshot) LIKE ?", [$term]);
            });
        }

        return $query->orderByDesc("transaction_time")->orderByDesc("id");
    }

    private function transactionData(Order $order, string $timezone): array
    {
        return [
            "id" => $order->id,
            "invoice_code" => $order->invoice_code,
            "transaction_time" => $order->transaction_time?->setTimezone($timezone)->toIso8601String(),
            "cashier_id" => $order->cashier_id,
            "cashier_name" => $order->cashier_name_snapshot,
            "status" => $order->status,
            "order_type" => $order->order_type,
            "payment_method" => $order->payment_method,
            "payment_status" => $order->payment_status,
            "gross_amount" => $order->gross_amount,
            "discount_amount" => $order->discount_amount,
            "final_amount" => $order->final_amount,
        ];
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            "cash" => "Tunai",
            "qris" => "QRIS",
            "transfer" => "Transfer",
            null, "" => "Tidak dicatat",
            default => $method,
        };
    }

    private function orderStatusLabel(string $status): string
    {
        return match ($status) {
            "completed" => "Selesai",
            "cancelled" => "Dibatalkan",
            default => $status,
        };
    }

    private function historyFilterLabel(array $filters): string
    {
        $parts = [];

        if (! empty($filters["start_date"]) || ! empty($filters["end_date"])) {
            $parts[] = "Tanggal: ".($filters["start_date"] ?? "—")." – ".($filters["end_date"] ?? "—");
        }
        foreach (["status" => "Status", "payment_status" => "Pembayaran", "payment_method" => "Metode", "cashier_id" => "Kasir", "search" => "Pencarian"] as $key => $label) {
            if (! empty($filters[$key])) {
                $parts[] = $label.": ".$filters[$key];
            }
        }

        return $parts ? implode(" · ", $parts) : "Semua transaksi · tanpa filter tambahan";
    }

    private function eligibleOrders(CarbonImmutable $from, CarbonImmutable $until): Builder
    {
        return Order::query()
            ->where("status", "completed")
            ->where("payment_status", "paid")
            ->where("transaction_time", ">=", $this->databaseBoundary($from))
            ->where("transaction_time", "<", $this->databaseBoundary($until));
    }

    private function periodRange(string $period, array $filters): array
    {
        $timezone = config("business.timezone", "Asia/Jakarta");
        $today = CarbonImmutable::today($timezone);

        if ($period === "custom") {
            $from = CarbonImmutable::createFromFormat("!Y-m-d", $filters["start_date"], $timezone);
            $until = CarbonImmutable::createFromFormat("!Y-m-d", $filters["end_date"], $timezone)->addDay();

            return [$from, $until];
        }

        $from = match ($period) {
            "today" => $today,
            "week" => $today->startOfWeek(),
            "year" => $today->startOfYear(),
            default => $today->startOfMonth(),
        };
        $until = match ($period) {
            "today" => $from->addDay(),
            "week" => $from->addWeek(),
            "year" => $from->addYear(),
            default => $from->addMonth(),
        };

        return [$from, $until];
    }

    private function databaseBoundary(CarbonImmutable $date): string
    {
        $utc = $date->utc();

        return DB::connection()->getDriverName() === "pgsql"
            ? $utc->format("Y-m-d H:i:sP")
            : $utc->format("Y-m-d H:i:s");
    }

    private function salesChart(
        string $period,
        CarbonImmutable $from,
        CarbonImmutable $until,
    ): array {
        $timezone = config("business.timezone", "Asia/Jakarta");
        $dayCount = (int) $from->startOfDay()->diffInDays($until->startOfDay());
        $bucket = $period === "today" ? "hour" : (($period === "year" || $dayCount > 62) ? "month" : "day");
        [$expression, $bindings] = $this->bucketExpression($bucket, $timezone);

        $rows = $this->eligibleOrders($from, $until)
            ->selectRaw(
                "{$expression} AS bucket, SUM(final_amount) AS revenue, COUNT(*) AS transaction_count",
                $bindings,
            )
            ->groupBy("bucket")
            ->orderBy("bucket")
            ->get()
            ->keyBy(fn ($row) => (string) $row->bucket);

        $keys = $this->chartKeys($bucket, $from, $until);
        $labels = [];
        $revenue = [];
        $transactions = [];

        foreach ($keys as $key => $label) {
            $row = $rows->get((string) $key);
            $labels[] = $label;
            $revenue[] = (int) ($row->revenue ?? 0);
            $transactions[] = (int) ($row->transaction_count ?? 0);
        }

        return compact("labels", "revenue", "transactions");
    }

    private function bucketExpression(string $bucket, string $timezone): array
    {
        if (DB::connection()->getDriverName() === "pgsql") {
            $expression = match ($bucket) {
                "hour" => "EXTRACT(HOUR FROM transaction_time AT TIME ZONE ?)::integer",
                "month" => "TO_CHAR(transaction_time AT TIME ZONE ?, 'YYYY-MM')",
                default => "(transaction_time AT TIME ZONE ?)::date",
            };

            return [$expression, [$timezone]];
        }

        $expression = match ($bucket) {
            "hour" => "CAST(strftime('%H', transaction_time) AS INTEGER)",
            "month" => "strftime('%Y-%m', transaction_time)",
            default => "date(transaction_time)",
        };

        return [$expression, []];
    }

    private function chartKeys(
        string $bucket,
        CarbonImmutable $from,
        CarbonImmutable $until,
    ): array {
        if ($bucket === "hour") {
            return array_combine(range(0, 23), array_map(
                fn (int $hour): string => sprintf("%02d:00", $hour),
                range(0, 23),
            ));
        }

        $keys = [];
        $cursor = $bucket === "month" ? $from->startOfMonth() : $from->startOfDay();
        $limit = $until->startOfDay();

        while ($cursor < $limit) {
            $key = $bucket === "month" ? $cursor->format("Y-m") : $cursor->toDateString();
            $keys[$key] = $bucket === "month"
                ? $cursor->locale("id")->translatedFormat("M Y")
                : $cursor->locale("id")->translatedFormat("j M");
            $cursor = $bucket === "month" ? $cursor->addMonth() : $cursor->addDay();
        }

        return $keys;
    }

    private function topProducts(CarbonImmutable $from, CarbonImmutable $until): array
    {
        return DB::table("order_items")
            ->join("orders", "orders.id", "=", "order_items.order_id")
            ->where("orders.status", "completed")
            ->where("orders.payment_status", "paid")
            ->where("orders.transaction_time", ">=", $this->databaseBoundary($from))
            ->where("orders.transaction_time", "<", $this->databaseBoundary($until))
            ->selectRaw(
                "order_items.product_name_snapshot AS name, SUM(order_items.quantity) AS quantity, SUM(order_items.final_subtotal) AS revenue",
            )
            ->groupBy("order_items.product_name_snapshot")
            ->orderByDesc("quantity")
            ->orderByDesc("revenue")
            ->limit(5)
            ->get()
            ->map(fn ($product) => [
                "name" => $product->name,
                "quantity" => (int) $product->quantity,
                "revenue" => (int) $product->revenue,
            ])
            ->all();
    }
}
