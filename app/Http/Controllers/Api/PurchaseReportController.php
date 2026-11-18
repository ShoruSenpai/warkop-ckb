<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierPurchase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            "period" => "sometimes|in:week,month,year",
            "per_page" => "sometimes|integer|min:1|max:100",
            "cursor" => "sometimes|string|max:512",
        ]);

        [$from, $to] = $this->periodRange($filters["period"] ?? "month");

        $purchases = SupplierPurchase::query()
            ->select([
                "id",
                "invoice_number",
                "supplier_name",
                "purchase_date",
                "total_cost",
                "status",
            ])
            ->withCount("items")
            ->whereBetween("purchase_date", [$from, $to])
            ->orderByDesc("purchase_date")
            ->orderByDesc("id")
            ->cursorPaginate(
                $filters["per_page"] ?? 20,
                ["*"],
                "cursor",
                $filters["cursor"] ?? null,
            );

        return response()->json([
            "success" => true,
            "message" => "Get Purchase Reports",
            "data" => $purchases->items(),
            "meta" => [
                "next_cursor" => $purchases->nextCursor()?->encode(),
                "previous_cursor" => $purchases->previousCursor()?->encode(),
                "per_page" => $purchases->perPage(),
                "has_more" => $purchases->hasMorePages(),
            ],
        ]);
    }

    public function summary(Request $request)
    {
        $filters = $request->validate([
            "period" => "sometimes|in:week,month,year",
            "chart_period" => "sometimes|in:month,year",
            "section" => "sometimes|in:all,overview,metrics,chart",
        ]);

        $section = $filters["section"] ?? "all";
        $data = [];

        if (in_array($section, ["all", "overview", "metrics"], true)) {
            $period = $filters["period"] ?? "month";
            [$from, $to] = $this->periodRange($period);

            $purchases = SupplierPurchase::query()->whereBetween(
                "purchase_date",
                [$from, $to],
            );

            $total = (clone $purchases)->sum("total_cost");
            $invoiceCount = (clone $purchases)->count();
            $receivedCount = (clone $purchases)
                ->where("status", "received")
                ->count();

            $data = [
                "period" => $period,
                "metrics" => [
                    "total_purchases" => (int) $total,
                    "invoice_count" => $invoiceCount,
                    "received_count" => $receivedCount,
                    "average_purchase" => $invoiceCount
                        ? (int) round($total / $invoiceCount)
                        : 0,
                ],
            ];

            if (in_array($section, ["all", "overview"], true)) {
                $data["top_suppliers"] = (clone $purchases)
                    ->selectRaw(
                        "supplier_name, SUM(total_cost) as total_cost, COUNT(*) as invoice_count",
                    )
                    ->groupBy("supplier_name")
                    ->orderByDesc("total_cost")
                    ->limit(5)
                    ->get();
            }
        }

        if (in_array($section, ["all", "chart"], true)) {
            $chartPeriod = $filters["chart_period"] ?? "month";
            $data["chart"] = $this->spendingChart($chartPeriod);
        }

        return response()->json([
            "success" => true,
            "message" => "Get Purchase Report Summary",
            "data" => $data,
        ]);
    }

    public function show(SupplierPurchase $purchase)
    {
        $purchase->load([
            "items.rawMaterial:id,name,unit_measurement",
            "items.rawMaterialPackaging:id,purchase_unit,conversion_factor",
            "items.product:id,name",
            "items.productPackaging:id,purchase_unit,conversion_factor",
        ]);

        return response()->json([
            "success" => true,
            "message" => "Get Purchase Invoice",
            "data" => $purchase,
        ]);
    }

    private function periodRange(string $period): array
    {
        $today = Carbon::today();

        $from = match ($period) {
            "week" => $today->copy()->subDays(6),
            "year" => $today->copy()->startOfYear(),
            default => $today->copy()->startOfMonth(),
        };

        return [$from->toDateString(), $today->toDateString()];
    }

    private function spendingChart(string $period): array
    {
        $today = Carbon::today();
        $from =
            $period === "year"
                ? $today->copy()->startOfYear()
                : $today->copy()->startOfMonth()->subMonths(5);

        $points =
            $period === "year"
                ? collect(range(1, $today->month))->mapWithKeys(
                    fn($month) => [$month => 0],
                )
                : collect(range(0, 5))->mapWithKeys(
                    fn($offset) => [
                        $from->copy()->addMonths($offset)->format("Y-m") => 0,
                    ],
                );

        $purchases = SupplierPurchase::query()
            ->select(["purchase_date", "total_cost"])
            ->whereBetween("purchase_date", [
                $from->toDateString(),
                $today->toDateString(),
            ])
            ->get();

        foreach ($purchases as $purchase) {
            $date = Carbon::parse($purchase->purchase_date);
            $key =
                $period === "year"
                    ? (int) $date->format("n")
                    : $date->format("Y-m");

            if ($points->has($key)) {
                $points[$key] += (int) $purchase->total_cost;
            }
        }

        $labels =
            $period === "year"
                ? collect(range(1, $today->month))->map(
                    fn($month) => Carbon::createFromDate(
                        $today->year,
                        $month,
                        1,
                    )
                        ->locale("id")
                        ->translatedFormat("M"),
                )
                : $points
                    ->keys()
                    ->map(
                        fn($month) => Carbon::createFromFormat("Y-m", $month)
                            ->locale("id")
                            ->translatedFormat("M"),
                    );

        return [
            "period" => $period,
            "labels" => $labels->values(),
            "values" => $points->values(),
        ];
    }
}
