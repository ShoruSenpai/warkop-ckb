<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesReportSummaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set("database.default", "sqlite");
        config()->set("database.connections.sqlite.database", ":memory:");
        DB::purge("sqlite");
        DB::setDefaultConnection("sqlite");

        Schema::create("orders", function (Blueprint $table): void {
            $table->id();
            $table->string("invoice_code");
            $table->foreignId("cashier_shift_id")->default(1);
            $table->foreignId("cashier_id")->nullable();
            $table->string("cashier_name_snapshot");
            $table->string("store_name_snapshot");
            $table->string("status");
            $table->string("order_type");
            $table->bigInteger("gross_amount");
            $table->bigInteger("discount_amount");
            $table->bigInteger("final_amount");
            $table->string("payment_method");
            $table->string("payment_status");
            $table->timestampTz("transaction_time");
            $table->timestampTz("paid_at")->nullable();
            $table->timestampTz("receipt_issued_at")->nullable();
            $table->timestampsTz();
        });

        Schema::create("order_items", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("order_id");
            $table->foreignId("product_id")->nullable();
            $table->string("product_name_snapshot");
            $table->text("variant_summary_snapshot")->nullable();
            $table->integer("quantity");
            $table->bigInteger("unit_price");
            $table->bigInteger("option_price");
            $table->bigInteger("subtotal");
            $table->bigInteger("discount_amount");
            $table->bigInteger("final_subtotal");
        });

        Schema::create("order_item_options", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("order_item_id");
            $table->foreignId("option_value_id")->nullable();
            $table->string("option_group_name_snapshot");
            $table->string("option_name_snapshot");
            $table->bigInteger("extra_price_snapshot");
        });

        Schema::create("order_discounts", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("order_id");
            $table->string("discount_name_snapshot");
            $table->string("discount_type_snapshot");
            $table->decimal("discount_value_snapshot", 12, 2);
            $table->bigInteger("discount_amount");
            $table->timestampTz("created_at");
        });

        Schema::create("order_item_discounts", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("order_item_id");
            $table->foreignId("product_discount_id")->nullable();
            $table->string("discount_name_snapshot");
            $table->string("discount_type_snapshot");
            $table->decimal("discount_value_snapshot", 12, 2);
            $table->bigInteger("discount_amount");
            $table->timestampTz("created_at");
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists("order_item_discounts");
        Schema::dropIfExists("order_discounts");
        Schema::dropIfExists("order_item_options");
        Schema::dropIfExists("order_items");
        Schema::dropIfExists("orders");

        parent::tearDown();
    }

    public function test_summary_includes_only_completed_paid_orders_and_uses_item_snapshots(): void
    {
        $this->insertOrder(1, "completed", "paid", 10000, "2026-11-20 10:00:00");
        $this->insertOrder(2, "cancelled", "cancelled", 20000, "2026-11-20 11:00:00");
        $this->insertOrder(3, "completed", "unpaid", 30000, "2026-11-20 12:00:00");
        DB::table("order_items")->insert([
            ["order_id" => 1, "product_name_snapshot" => "Kopi Historis", "quantity" => 2, "unit_price" => 5000, "option_price" => 0, "subtotal" => 10000, "discount_amount" => 0, "final_subtotal" => 10000],
            ["order_id" => 2, "product_name_snapshot" => "Batal", "quantity" => 7, "unit_price" => 20000, "option_price" => 0, "subtotal" => 20000, "discount_amount" => 0, "final_subtotal" => 20000],
        ]);

        $response = $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales/summary?period=month&section=all")
            ->assertOk()
            ->assertJsonPath("success", true)
            ->assertJsonPath("data.metrics.total_sales.current", 10000)
            ->assertJsonPath("data.metrics.transaction_count.current", 1)
            ->assertJsonPath("data.metrics.average_ticket.current", 10000)
            ->assertJsonPath("data.products.0.name", "Kopi Historis")
            ->assertJsonPath("data.products.0.quantity", 2)
            ->assertJsonPath("data.products.0.revenue", 10000);

        $this->assertSame(10000, array_sum($response->json("data.chart.revenue")));
        $this->assertSame(1, array_sum($response->json("data.chart.transactions")));
    }

    public function test_summary_rejects_invalid_period_and_non_admin_roles(): void
    {
        $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales/summary?period=decade")
            ->assertUnprocessable();

        $this->actingAs($this->user("cashier"), "sanctum")
            ->getJson("/api/reports/sales/summary")
            ->assertForbidden();
    }

    public function test_custom_period_includes_start_and_excludes_next_wib_day(): void
    {
        $this->insertOrder(1, "completed", "paid", 10000, "2026-11-20 16:59:59");
        $this->insertOrder(2, "completed", "paid", 20000, "2026-11-20 17:00:00");

        $this->actingAs($this->user("admin"), "sanctum")
            ->getJson("/api/reports/sales/summary?period=custom&start_date=2026-11-20&end_date=2026-11-20&section=metrics")
            ->assertOk()
            ->assertJsonPath("data.metrics.total_sales.current", 10000)
            ->assertJsonPath("data.metrics.transaction_count.current", 1);
    }

    public function test_empty_period_returns_zero_metrics_and_empty_top_products(): void
    {
        $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales/summary?period=month&section=all")
            ->assertOk()
            ->assertJsonPath("data.metrics.total_sales.current", 0)
            ->assertJsonPath("data.metrics.transaction_count.current", 0)
            ->assertJsonPath("data.metrics.average_ticket.current", 0)
            ->assertJsonPath("data.products", []);
    }

    public function test_transaction_list_includes_all_statuses_and_filters_with_stable_pagination(): void
    {
        $this->insertOrder(1, "completed", "paid", 10000, "2026-11-20 10:00:00");
        $this->insertOrder(2, "cancelled", "cancelled", 20000, "2026-11-20 11:00:00");
        $this->insertOrder(3, "completed", "unpaid", 30000, "2026-11-20 12:00:00");

        $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales?per_page=2&page=1")
            ->assertOk()
            ->assertJsonCount(2, "data.transactions")
            ->assertJsonPath("data.transactions.0.id", 3)
            ->assertJsonPath("data.transactions.0.status", "completed")
            ->assertJsonPath("data.transactions.0.payment_status", "unpaid")
            ->assertJsonPath("data.transactions.0.cashier_name", "Kasir")
            ->assertJsonPath("meta.current_page", 1)
            ->assertJsonPath("meta.total", 3)
            ->assertJsonPath("meta.last_page", 2)
            ->assertJsonPath("meta.filters.statuses", ["cancelled", "completed"])
            ->assertJsonPath("meta.filters.payment_statuses", ["cancelled", "paid", "unpaid"])
            ->assertJsonPath("meta.filters.payment_methods", ["cash"])
            ->assertJsonPath("meta.filters.cashiers.0.id", 7)
            ->assertJsonPath("meta.filters.cashiers.0.name", "Kasir");

        $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales?per_page=2&page=2")
            ->assertOk()
            ->assertJsonCount(1, "data.transactions")
            ->assertJsonPath("data.transactions.0.id", 1)
            ->assertJsonPath("meta.current_page", 2);

        $this->actingAs($this->user("admin"), "sanctum")
            ->getJson("/api/reports/sales?status=cancelled&payment_status=cancelled&search=INV-2")
            ->assertOk()
            ->assertJsonCount(1, "data.transactions")
            ->assertJsonPath("data.transactions.0.id", 2);

        $this->actingAs($this->user("admin"), "sanctum")
            ->getJson("/api/reports/sales?cashier_id=7&start_date=2026-11-20&end_date=2026-11-20")
            ->assertOk()
            ->assertJsonCount(3, "data.transactions");
    }

    public function test_transaction_list_validates_filters_and_page_size(): void
    {
        $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales?start_date=2026-11-22&end_date=2026-11-20&per_page=500")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["end_date", "per_page"]);

        $this->actingAs($this->user("cashier"), "sanctum")
            ->getJson("/api/reports/sales")
            ->assertForbidden();
    }

    public function test_transaction_detail_returns_only_historical_item_option_and_discount_snapshots(): void
    {
        $this->insertOrder(1, "completed", "paid", 18000, "2026-11-20 10:00:00");
        DB::table("orders")->where("id", 1)->update(["discount_amount" => 2000]);
        DB::table("order_items")->insert([
            [
                "id" => 11,
                "order_id" => 1,
                "product_id" => null,
                "product_name_snapshot" => "Produk Lama",
                "variant_summary_snapshot" => "Ukuran: Besar",
                "quantity" => 2,
                "unit_price" => 9000,
                "option_price" => 1000,
                "subtotal" => 20000,
                "discount_amount" => 0,
                "final_subtotal" => 20000,
            ],
            [
                "id" => 12,
                "order_id" => 1,
                "product_id" => null,
                "product_name_snapshot" => "Produk Tidak Aktif",
                "variant_summary_snapshot" => null,
                "quantity" => 1,
                "unit_price" => 5000,
                "option_price" => 0,
                "subtotal" => 5000,
                "discount_amount" => 0,
                "final_subtotal" => 5000,
            ],
        ]);
        DB::table("order_item_options")->insert([
            "order_item_id" => 11,
            "option_value_id" => null,
            "option_group_name_snapshot" => "Ukuran",
            "option_name_snapshot" => "Besar",
            "extra_price_snapshot" => 1000,
        ]);
        DB::table("order_discounts")->insert([
            "order_id" => 1,
            "discount_name_snapshot" => "Diskon kasir",
            "discount_type_snapshot" => "fixed",
            "discount_value_snapshot" => 2000,
            "discount_amount" => 2000,
            "created_at" => "2026-11-20 10:00:00",
        ]);
        DB::table("order_item_discounts")->insert([
            "order_item_id" => 11,
            "product_discount_id" => null,
            "discount_name_snapshot" => "Diskon historis item",
            "discount_type_snapshot" => "fixed",
            "discount_value_snapshot" => 0,
            "discount_amount" => 0,
            "created_at" => "2026-11-20 10:00:00",
        ]);

        $this->actingAs($this->user("owner"), "sanctum")
            ->getJson("/api/reports/sales/1")
            ->assertOk()
            ->assertJsonPath("data.cashier_name", "Kasir")
            ->assertJsonPath("data.items.0.product_name", "Produk Lama")
            ->assertJsonPath("data.items.0.variant_summary_snapshot", "Ukuran: Besar")
            ->assertJsonPath("data.items.0.options.0.option_name_snapshot", "Besar")
            ->assertJsonPath("data.items.0.discounts.0.discount_name_snapshot", "Diskon historis item")
            ->assertJsonPath("data.items.1.product_name", "Produk Tidak Aktif")
            ->assertJsonPath("data.items.1.options", [])
            ->assertJsonPath("data.discounts.0.discount_name_snapshot", "Diskon kasir");
    }

    public function test_transaction_detail_returns_not_found_for_unknown_order(): void
    {
        $this->actingAs($this->user("admin"), "sanctum")
            ->getJson("/api/reports/sales/999")
            ->assertNotFound();
    }

    private function user(string $role): User
    {
        $user = new User();
        $user->forceFill(["id" => 1, "role" => $role]);

        return $user;
    }

    private function insertOrder(int $id, string $status, string $paymentStatus, int $amount, string $time): void
    {
        DB::table("orders")->insert([
            "id" => $id,
            "invoice_code" => "INV-{$id}",
            "cashier_id" => 7,
            "cashier_name_snapshot" => "Kasir",
            "store_name_snapshot" => "Warkop Cak Kebo",
            "status" => $status,
            "order_type" => "dinein",
            "gross_amount" => $amount,
            "discount_amount" => 0,
            "final_amount" => $amount,
            "payment_method" => "cash",
            "payment_status" => $paymentStatus,
            "transaction_time" => $time,
            "paid_at" => $paymentStatus === "paid" ? $time : null,
            "receipt_issued_at" => null,
            "created_at" => $time,
            "updated_at" => $time,
        ]);
    }
}
