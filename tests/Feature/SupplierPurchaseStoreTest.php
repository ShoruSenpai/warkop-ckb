<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\SupplierPurchaseController;
use App\Http\Controllers\Api\PurchaseReportController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;
use Tests\TestCase;

class SupplierPurchaseStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create("raw_materials", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("unit_measurement");
            $table->bigInteger("current_stock")->default(0);
            $table->timestampsTz();
        });
        Schema::create("raw_material_packagings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("raw_material_id");
            $table->string("purchase_unit");
            $table->bigInteger("conversion_factor");
            $table->boolean("is_active")->default(true);
        });
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("stock_type")->default("static");
            $table->bigInteger("stock")->default(0);
            $table->timestampsTz();
        });
        Schema::create("product_packagings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->string("purchase_unit");
            $table->bigInteger("conversion_factor");
            $table->boolean("is_active")->default(true);
        });
        Schema::create("supplier_purchases", function (Blueprint $table) {
            $table->id();
            $table->string("invoice_number", 50)->unique();
            $table->string("supplier_name", 100);
            $table->bigInteger("total_cost");
            $table->date("purchase_date");
            $table->string("status", 20);
            $table->timestampsTz();
        });
        Schema::create("supplier_purchase_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("purchase_id");
            $table->string("item_type", 20);
            $table->foreignId("raw_material_id")->nullable();
            $table->foreignId("raw_material_packaging_id")->nullable();
            $table->foreignId("product_id")->nullable();
            $table->foreignId("product_packaging_id")->nullable();
            $table->string("item_name_snapshot", 100);
            $table->string("packaging_name_snapshot", 100);
            $table->string("purchase_unit", 50);
            $table->bigInteger("quantity");
            $table->bigInteger("conversion_factor");
            $table->bigInteger("unit_price");
            $table->bigInteger("subtotal");
        });

        DB::table("raw_materials")->insert([
            "id" => 1,
            "name" => "Kopi",
            "unit_measurement" => "gram",
            "current_stock" => 10,
        ]);
        DB::table("raw_material_packagings")->insert([
            "id" => 1,
            "raw_material_id" => 1,
            "purchase_unit" => "karung",
            "conversion_factor" => 1000,
            "is_active" => true,
        ]);
        DB::table("supplier_purchase_items")->delete();
        DB::table("supplier_purchases")->delete();
        DB::table("raw_materials")->where("id", 1)->update(["current_stock" => 10]);
    }

    public function test_pending_purchase_saves_snapshots_without_increasing_stock(): void
    {
        $response = $this->storePurchase("pending");

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(10, (int) DB::table("raw_materials")->value("current_stock"));
        $this->assertDatabaseHas("supplier_purchase_items", [
            "item_type" => "raw_material",
            "item_name_snapshot" => "Kopi",
            "packaging_name_snapshot" => "karung",
            "quantity" => 2,
            "conversion_factor" => 1000,
        ]);
    }

    public function test_received_purchase_increases_stock_by_integer_conversion_once(): void
    {
        $response = $this->storePurchase("received");

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(2010, (int) DB::table("raw_materials")->value("current_stock"));
        $this->assertSame(1, DB::table("supplier_purchases")->count());
    }

    public function test_invalid_status_is_rejected(): void
    {
        try {
            $this->storePurchase("cancelled");
            $this->fail("Invalid status should be rejected.");
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey("status", $exception->errors());
        }
        $this->assertSame(0, DB::table("supplier_purchases")->count());
    }

    public function test_repeating_received_invoice_does_not_increase_stock_twice(): void
    {
        $firstResponse = $this->storePurchase("received");

        $this->assertSame(201, $firstResponse->getStatusCode());
        try {
            $this->storePurchase("received");
            $this->fail("Duplicate invoice should be rejected.");
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey("invoice_number", $exception->errors());
        }
        $this->assertSame(2010, (int) DB::table("raw_materials")->value("current_stock"));
        $this->assertSame(1, DB::table("supplier_purchases")->count());
    }

    public function test_stock_failure_rolls_back_invoice_and_items(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER prevent_stock_overflow
            BEFORE UPDATE OF current_stock ON raw_materials
            WHEN NEW.current_stock > 9999999999
            BEGIN
                SELECT RAISE(ABORT, 'stock limit exceeded');
            END;
        SQL);
        DB::table("raw_materials")->where("id", 1)->update([
            "current_stock" => 9999999999,
        ]);

        $response = $this->storePurchase("received");

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(9999999999, (int) DB::table("raw_materials")->value("current_stock"));
        $this->assertSame(0, DB::table("supplier_purchases")->count());
        $this->assertSame(0, DB::table("supplier_purchase_items")->count());
    }

    public function test_purchase_report_includes_invoice_status(): void
    {
        foreach (["pending", "received"] as $status) {
            DB::table("supplier_purchases")->insert([
                "invoice_number" => "INV-" . $status,
                "supplier_name" => "Supplier",
                "total_cost" => 15000,
                "purchase_date" => now()->toDateString(),
                "status" => $status,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $response = app(PurchaseReportController::class)->index(
            Request::create("/api/reports/purchases?period=month"),
        );

        $statuses = collect($response->getData(true)["data"])
            ->pluck("status")
            ->sort()
            ->values()
            ->all();

        $this->assertSame(["pending", "received"], $statuses);
    }

    public function test_purchase_pdf_uses_existing_summary_and_history_rules(): void
    {
        Pdf::fake();
        foreach (["pending", "received"] as $index => $status) {
            DB::table("supplier_purchases")->insert([
                "invoice_number" => "PDF-{$status}",
                "supplier_name" => "Supplier",
                "total_cost" => ($index + 1) * 10000,
                "purchase_date" => now()->toDateString(),
                "status" => $status,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $this->actingAs($this->user("owner"))
            ->get("/reports/purchases/pdf?period=month&chart_period=year")
            ->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf): bool {
            return preg_match('/^laporan-pembelian-\d{2}-\d{2}-\d{4}-\d{2}-\d{2}\.pdf$/', $pdf->downloadName) === 1
                && $pdf->viewData["metrics"]["total_purchases"] === 30000
                && $pdf->viewData["metrics"]["invoice_count"] === 2
                && $pdf->viewData["metrics"]["received_count"] === 1
                && $pdf->viewData["purchases"]->count() === 2;
        });
    }

    public function test_purchase_pdf_validates_filters_and_restricts_roles(): void
    {
        Pdf::fake();

        $this->actingAs($this->user("owner"))
            ->getJson("/reports/purchases/pdf?period=decade")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["period", "chart_period"]);

        $this->actingAs($this->user("cashier"))
            ->getJson("/reports/purchases/pdf")
            ->assertStatus(302);
    }

    #[Group("chromium")]
    public function test_purchase_pdf_renders_with_real_chromium(): void
    {
        config()->set("laravel-pdf.driver", "browsershot");
        DB::table("supplier_purchases")->insert([
            "invoice_number" => "PDF-CHROMIUM",
            "supplier_name" => "Supplier",
            "total_cost" => 10000,
            "purchase_date" => now()->toDateString(),
            "status" => "received",
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $response = $this->actingAs($this->user("owner"))
            ->get("/reports/purchases/pdf?period=month&chart_period=month");

        $response->assertOk();
        $response->assertHeader("content-type", "application/pdf");
        $response->assertHeader("content-disposition");
        $this->assertStringStartsWith("%PDF-", $response->getContent());
    }

    #[Group("chromium")]
    public function test_medium_and_large_purchase_pdfs_paginate_with_real_chromium(): void
    {
        config()->set("laravel-pdf.driver", "browsershot");
        $this->actingAs($this->user("owner"));

        for ($index = 1; $index <= 40; $index++) {
            $this->insertPdfPurchase($index);
        }

        $medium = $this->get("/reports/purchases/pdf?period=month&chart_period=month");
        $this->assertPdfHasMultiplePages($medium->assertOk()->getContent());

        for ($index = 41; $index <= 160; $index++) {
            $this->insertPdfPurchase($index);
        }

        $large = $this->get("/reports/purchases/pdf?period=month&chart_period=month");
        $this->assertPdfHasMultiplePages($large->assertOk()->getContent());
    }

    public function test_pending_purchase_can_be_received_once(): void
    {
        $purchaseResponse = $this->storePurchase("pending");
        $purchaseId = $purchaseResponse->getData(true)["data"]["id"];

        $first = app(SupplierPurchaseController::class)->updateStatus(
            Request::create("/api/supplier-purchases/{$purchaseId}/status", "PUT", ["status" => "received"]),
            \App\Models\SupplierPurchase::findOrFail($purchaseId),
        );
        $second = app(SupplierPurchaseController::class)->updateStatus(
            Request::create("/api/supplier-purchases/{$purchaseId}/status", "PUT", ["status" => "received"]),
            \App\Models\SupplierPurchase::findOrFail($purchaseId),
        );

        $this->assertSame(200, $first->getStatusCode());
        $this->assertSame(200, $second->getStatusCode());
        $this->assertSame(2010, (int) DB::table("raw_materials")->value("current_stock"));
    }

    public function test_received_purchase_cannot_return_to_pending(): void
    {
        $purchaseResponse = $this->storePurchase("received");
        $purchaseId = $purchaseResponse->getData(true)["data"]["id"];

        $response = app(SupplierPurchaseController::class)->updateStatus(
            Request::create("/api/supplier-purchases/{$purchaseId}/status", "PUT", ["status" => "pending"]),
            \App\Models\SupplierPurchase::findOrFail($purchaseId),
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame("received", DB::table("supplier_purchases")->value("status"));
        $this->assertSame(2010, (int) DB::table("raw_materials")->value("current_stock"));
    }

    public function test_pending_invoice_delete_removes_header_and_items_without_stock_change(): void
    {
        $purchase = $this->storePurchase("pending")->getData(true)["data"];
        $response = app(SupplierPurchaseController::class)->destroy(
            \App\Models\SupplierPurchase::findOrFail($purchase["id"]),
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(0, DB::table("supplier_purchases")->count());
        $this->assertSame(0, DB::table("supplier_purchase_items")->count());
        $this->assertSame(10, (int) DB::table("raw_materials")->value("current_stock"));
    }

    public function test_received_invoice_delete_is_rejected(): void
    {
        $purchase = $this->storePurchase("received")->getData(true)["data"];
        $response = app(SupplierPurchaseController::class)->destroy(
            \App\Models\SupplierPurchase::findOrFail($purchase["id"]),
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(1, DB::table("supplier_purchases")->count());
        $this->assertSame(1, DB::table("supplier_purchase_items")->count());
        $this->assertSame(2010, (int) DB::table("raw_materials")->value("current_stock"));
    }

    public function test_invoice_detail_returns_saved_name_snapshots(): void
    {
        $purchase = $this->storePurchase("pending")->getData(true)["data"];
        DB::table("raw_materials")->where("id", 1)->update(["name" => "Kopi Baru"]);
        DB::table("raw_material_packagings")->where("id", 1)->update(["purchase_unit" => "dus"]);

        $response = app(PurchaseReportController::class)->show(
            \App\Models\SupplierPurchase::findOrFail($purchase["id"]),
        );
        $item = $response->getData(true)["data"]["items"][0];

        $this->assertSame("Kopi", $item["item_name_snapshot"]);
        $this->assertSame("karung", $item["packaging_name_snapshot"]);
    }

    public function test_weekly_invoice_filter_uses_jakarta_date_boundaries(): void
    {
        \Carbon\Carbon::setTestNow(new \DateTimeImmutable("2026-11-20 18:00:00 UTC"));
        foreach (["2026-11-14", "2026-11-15", "2026-11-21"] as $index => $date) {
            DB::table("supplier_purchases")->insert([
                "invoice_number" => "DATE-{$index}",
                "supplier_name" => "Supplier",
                "total_cost" => 100,
                "purchase_date" => $date,
                "status" => "received",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $response = app(PurchaseReportController::class)->index(
            Request::create("/api/reports/purchases?period=week"),
        );

        \Carbon\Carbon::setTestNow();
        $dates = collect($response->getData(true)["data"])
            ->pluck("purchase_date")
            ->map(fn ($date) => substr((string) $date, 0, 10))
            ->sort()
            ->values()
            ->all();

        $this->assertSame(["2026-11-15", "2026-11-21"], $dates);
    }

    private function storePurchase(string $status)
    {
        $request = Request::create("/api/supplier-purchases", "POST", [
            "invoice_number" => "INV-2026-0001",
            "supplier_name" => "Supplier",
            "purchase_date" => now()->toDateString(),
            "status" => $status,
            "items" => [[
                "raw_material_id" => 1,
                "raw_material_packaging_id" => 1,
                "quantity" => 2,
                "unit_price" => 15000,
            ]],
        ]);

        return app(SupplierPurchaseController::class)->store($request);
    }

    private function user(string $role): User
    {
        $user = new User();
        $user->forceFill(["id" => 1, "role" => $role]);

        return $user;
    }

    private function insertPdfPurchase(int $index): void
    {
        DB::table("supplier_purchases")->insert([
            "invoice_number" => "PDF-SIZE-{$index}",
            "supplier_name" => "Supplier",
            "total_cost" => 10000,
            "purchase_date" => now()->toDateString(),
            "status" => "received",
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }

    private function assertPdfHasMultiplePages(string $content): void
    {
        $this->assertStringStartsWith("%PDF-", $content);
        preg_match_all("~/Type\\s*/Page\\b~", $content, $matches);
        $this->assertGreaterThanOrEqual(2, count($matches[0]));
    }
}
