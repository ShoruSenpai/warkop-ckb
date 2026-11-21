<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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
        if (!empty($filters["start_date"])) {
            $from = CarbonImmutable::createFromFormat("!Y-m-d", $filters["start_date"], $timezone);
            $query->where("transaction_time", ">=", $this->databaseBoundary($from));
        }
        if (!empty($filters["end_date"])) {
            $until = CarbonImmutable::createFromFormat("!Y-m-d", $filters["end_date"], $timezone)->addDay();
            $query->where("transaction_time", "<", $this->databaseBoundary($until));
        }

        foreach (["status", "payment_status", "payment_method", "cashier_id"] as $filter) {
            if (isset($filters[$filter]) && $filters[$filter] !== "") {
                $query->where($filter, $filters[$filter]);
            }
        }

        if (!empty($filters["search"])) {
            $term = "%" . mb_strtolower(trim($filters["search"])) . "%";
            $query->where(function (Builder $query) use ($term): void {
                $query
                    ->whereRaw("LOWER(invoice_code) LIKE ?", [$term])
                    ->orWhereRaw("LOWER(cashier_name_snapshot) LIKE ?", [$term]);
            });
        }

        $orders = $query
            ->orderByDesc("transaction_time")
            ->orderByDesc("id")
            ->paginate($filters["per_page"] ?? 10, ["*"], "page", $filters["page"] ?? 1);

        $transactions = $orders->getCollection()->map(function (Order $order) use ($timezone): array {
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
        })->all();

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
        ]);

        $period = $filters["period"] ?? "month";
        $section = $filters["section"] ?? "all";
        [$from, $until] = $this->periodRange($period, $filters);
        $data = [];

        if (in_array($section, ["all", "overview", "metrics"], true)) {
            $metrics = $this->eligibleOrders($from, $until)
                ->selectRaw(
                    "COALESCE(SUM(final_amount), 0) AS total_sales, COUNT(*) AS transaction_count",
                )
                ->first();

            $totalSales = (int) $metrics->total_sales;
            $transactionCount = (int) $metrics->transaction_count;

            $data["period"] = $period;
            $data["metrics"] = [
                "total_sales" => [
                    "current" => $totalSales,
                    "note" => "Pada periode terpilih",
                ],
                "transaction_count" => [
                    "current" => $transactionCount,
                    "note" => "Order selesai dan dibayar",
                ],
                "average_ticket" => [
                    "current" => $transactionCount
                        ? (int) round($totalSales / $transactionCount)
                        : 0,
                    "note" => "Rata-rata nilai order dibayar",
                ],
            ];
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
