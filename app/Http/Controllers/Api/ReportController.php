<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\SupplierPurchase;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get real-time summary for dashboard metrics and chart.
     */
    public function dashboardSummary(Request $request): JsonResponse
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Omzet Hari Ini & Kemarin
        $todayOmzet = (float) Order::whereDate('transaction_time', $today)->sum('final_amount');
        $yesterdayOmzet = (float) Order::whereDate('transaction_time', $yesterday)->sum('final_amount');
        $omzetDiffPercent = $yesterdayOmzet > 0
            ? round((($todayOmzet - $yesterdayOmzet) / $yesterdayOmzet) * 100, 1)
            : ($todayOmzet > 0 ? 100 : 0);

        // Transaksi Hari Ini & Kemarin
        $todayTxCount = Order::whereDate('transaction_time', $today)->count();
        $yesterdayTxCount = Order::whereDate('transaction_time', $yesterday)->count();
        $txDiffCount = $todayTxCount - $yesterdayTxCount;

        // Produk
        $activeProductsCount = Product::where('is_active', true)->count();
        $totalProductsCount = Product::count();

        // Stok Kritis Bahan Baku
        $criticalStockCount = RawMaterial::whereRaw('stock <= minimum_stock')->count();

        // Grafik 7 Hari Terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateFormatted = $date->format('Y-m-d');
            $dayLabel = $date->translatedFormat('D, d M');

            $dayOmzet = (float) Order::whereDate('transaction_time', $dateFormatted)->sum('final_amount');
            $dayTx = Order::whereDate('transaction_time', $dateFormatted)->count();

            $chartData[] = [
                'date' => $dateFormatted,
                'label' => $dayLabel,
                'omzet' => $dayOmzet,
                'transactions' => $dayTx,
            ];
        }

        // Transaksi Terakhir
        $recentTransactions = Order::with('cashier')
            ->orderBy('transaction_time', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'today_omzet' => $todayOmzet,
                'yesterday_omzet' => $yesterdayOmzet,
                'omzet_diff_percent' => $omzetDiffPercent,
                'today_tx_count' => $todayTxCount,
                'tx_diff_count' => $txDiffCount,
                'active_products_count' => $activeProductsCount,
                'total_products_count' => $totalProductsCount,
                'critical_stock_count' => $criticalStockCount,
                'chart_data' => $chartData,
                'recent_transactions' => $recentTransactions,
            ],
        ]);
    }

    /**
     * Get Transaction History with filters.
     */
    public function transactions(Request $request): JsonResponse
    {
        $query = Order::with(['cashier', 'cashierShift', 'orderItems.product']);

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_time', '<=', $request->end_date);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('invoice_code', 'like', "%{$search}%");
        }

        $orders = $query->orderBy('transaction_time', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Get Sales Report data.
     */
    public function sales(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $ordersQuery = Order::whereDate('transaction_time', '>=', $startDate)
            ->whereDate('transaction_time', '<=', $endDate);

        $grossRevenue = (float) (clone $ordersQuery)->sum('gross_amount');
        $discountAmount = (float) (clone $ordersQuery)->sum('discount_amount');
        $netRevenue = (float) (clone $ordersQuery)->sum('final_amount');
        $totalOrders = (clone $ordersQuery)->count();
        $avgOrderValue = $totalOrders > 0 ? round($netRevenue / $totalOrders, 2) : 0;

        // Daily trend breakdown
        $dailyTrend = Order::select(
            DB::raw('DATE(transaction_time) as date'),
            DB::raw('SUM(final_amount) as total_sales'),
            DB::raw('COUNT(id) as total_orders')
        )
            ->whereDate('transaction_time', '>=', $startDate)
            ->whereDate('transaction_time', '<=', $endDate)
            ->groupBy(DB::raw('DATE(transaction_time)'))
            ->orderBy('date', 'asc')
            ->get();

        // Top Selling Products
        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereDate('transaction_time', '>=', $startDate)
                    ->whereDate('transaction_time', '<=', $endDate);
            })
            ->with(['product.category'])
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'gross_revenue' => $grossRevenue,
                    'discount_amount' => $discountAmount,
                    'net_revenue' => $netRevenue,
                    'total_orders' => $totalOrders,
                    'avg_order_value' => $avgOrderValue,
                ],
                'daily_trend' => $dailyTrend,
                'top_products' => $topProducts,
            ],
        ]);
    }

    /**
     * Get Purchase Report data.
     */
    public function purchases(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $purchasesQuery = SupplierPurchase::whereDate('purchase_date', '>=', $startDate)
            ->whereDate('purchase_date', '<=', $endDate);

        $totalSpent = (float) (clone $purchasesQuery)->sum('total_cost');
        $totalInvoices = (clone $purchasesQuery)->count();

        $purchases = (clone $purchasesQuery)
            ->with(['items.rawMaterial', 'items.rawMaterialPackaging'])
            ->orderBy('purchase_date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_spent' => $totalSpent,
                    'total_invoices' => $totalInvoices,
                ],
                'purchases' => $purchases,
            ],
        ]);
    }

    /**
     * Get Stock Report data.
     */
    public function stock(Request $request): JsonResponse
    {
        $materials = RawMaterial::with('packagings')->orderBy('name', 'asc')->get();

        $totalMaterials = $materials->count();
        $criticalCount = 0;
        $normalCount = 0;
        $outOfStockCount = 0;

        $formattedMaterials = $materials->map(function ($mat) use (&$criticalCount, &$normalCount, &$outOfStockCount) {
            $status = 'normal';
            if ($mat->stock <= 0) {
                $status = 'habis';
                $outOfStockCount++;
            } elseif ($mat->stock <= $mat->minimum_stock) {
                $status = 'kritis';
                $criticalCount++;
            } else {
                $normalCount++;
            }

            return [
                'id' => $mat->id,
                'name' => $mat->name,
                'category' => $mat->category,
                'stock' => (float) $mat->stock,
                'minimum_stock' => (float) $mat->minimum_stock,
                'unit' => $mat->unit,
                'status' => $status,
                'packagings_count' => $mat->packagings->count(),
            ];
        });

        $products = Product::with('category')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_materials' => $totalMaterials,
                    'critical_count' => $criticalCount,
                    'normal_count' => $normalCount,
                    'out_of_stock_count' => $outOfStockCount,
                ],
                'materials' => $formattedMaterials,
                'products' => $products,
            ],
        ]);
    }
}
