<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierPurchase;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $purchases = SupplierPurchase::query()
            ->select([
                "id",
                "invoice_number",
                "supplier_name",
                "purchase_date",
                "status",
            ])
            ->orderByDesc("purchase_date")
            ->orderByDesc("id")
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Get Purchase Reports",
            "data" => $purchases,
        ]);
    }
}
