<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderRelationshipTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create("employees", function (Blueprint $table): void {
            $table->id();
            $table->string("full_name");
        });

        Schema::create("products", function (Blueprint $table): void {
            $table->id();
            $table->string("name");
        });

        Schema::create("orders", function (Blueprint $table): void {
            $table->id();
            $table->string("invoice_code");
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
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists("order_item_options");
        Schema::dropIfExists("order_items");
        Schema::dropIfExists("orders");
        Schema::dropIfExists("products");
        Schema::dropIfExists("employees");

        parent::tearDown();
    }

    public function test_order_eager_loads_cashier_items_products_and_option_snapshots(): void
    {
        DB::table("employees")->insert(["id" => 7, "full_name" => "Kasir Satu"]);
        DB::table("products")->insert(["id" => 12, "name" => "Kopi"]);
        DB::table("orders")->insert($this->orderData(3, 7));
        DB::table("order_items")->insert($this->itemData(3, 22, 12));
        DB::table("order_item_options")->insert([
            "id" => 31,
            "order_item_id" => 22,
            "option_value_id" => null,
            "option_group_name_snapshot" => "Ukuran",
            "option_name_snapshot" => "Besar",
            "extra_price_snapshot" => 4000,
        ]);

        $order = Order::with(["cashier", "items.product", "items.options"])->findOrFail(3);

        $this->assertSame("Kasir Satu", $order->cashier->full_name);
        $this->assertSame("Latte Historis", $order->items[0]->product_name_snapshot);
        $this->assertSame("Kopi", $order->items[0]->product->name);
        $this->assertSame("Besar", $order->items[0]->options[0]->option_name_snapshot);
        $this->assertSame(4000, $order->items[0]->options[0]->extra_price_snapshot);
        $this->assertSame($order->id, $order->items[0]->order->id);
        $this->assertSame($order->items[0]->id, $order->items[0]->options[0]->orderItem->id);
    }

    public function test_order_item_without_options_keeps_snapshot_when_product_is_missing(): void
    {
        DB::table("orders")->insert($this->orderData(4));
        DB::table("order_items")->insert($this->itemData(4, 23));

        $item = Order::with("items.product", "items.options")
            ->findOrFail(4)
            ->items[0];

        $this->assertNull($item->product);
        $this->assertSame("Latte Historis", $item->product_name_snapshot);
        $this->assertTrue($item->options->isEmpty());
    }

    private function orderData(int $id, ?int $cashierId = null): array
    {
        return [
            "id" => $id,
            "invoice_code" => "INV-{$id}",
            "cashier_id" => $cashierId,
            "cashier_name_snapshot" => "Kasir Snapshot",
            "store_name_snapshot" => "Warkop Cak Kebo",
            "status" => "paid",
            "order_type" => "dine_in",
            "gross_amount" => 14000,
            "discount_amount" => 0,
            "final_amount" => 14000,
            "payment_method" => "cash",
            "payment_status" => "paid",
            "transaction_time" => "2026-11-20 10:00:00+07:00",
            "paid_at" => "2026-11-20 10:00:00+07:00",
            "receipt_issued_at" => null,
            "created_at" => "2026-11-20 10:00:00+07:00",
            "updated_at" => "2026-11-20 10:00:00+07:00",
        ];
    }

    private function itemData(int $orderId, int $id, ?int $productId = null): array
    {
        return [
            "id" => $id,
            "order_id" => $orderId,
            "product_id" => $productId,
            "product_name_snapshot" => "Latte Historis",
            "variant_summary_snapshot" => "Ukuran: Besar",
            "quantity" => 1,
            "unit_price" => 10000,
            "option_price" => 4000,
            "subtotal" => 14000,
            "discount_amount" => 0,
            "final_subtotal" => 14000,
        ];
    }
}
