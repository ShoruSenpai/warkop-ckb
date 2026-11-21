<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
        COMMENT ON TABLE users IS 'Login identities. Prefer deactivation over hard deletion.';
        COMMENT ON TABLE employees IS 'Employee master; user_id may be NULL when login access is detached.';
        COMMENT ON TABLE employee_schedules IS 'Employee schedules; schedule_date and time values follow WIB business calendar.';
        COMMENT ON TABLE attendances IS 'Attendance history; timestamps are absolute instants and are displayed in WIB.';
        COMMENT ON TABLE cashier_shifts IS 'Cashier shift history; do not hard-delete shifts referenced by orders.';
        COMMENT ON TABLE categories IS 'Product category master; deletion is restricted while products reference it.';
        COMMENT ON TABLE raw_materials IS 'Single-store raw-material master with current global stock.';
        COMMENT ON TABLE products IS 'Single-store product master; deleting a product must not delete historical order lines.';
        COMMENT ON TABLE product_packagings IS 'Product-owned packaging configuration; purchase lines retain packaging snapshots.';
        COMMENT ON TABLE raw_material_packagings IS 'Raw-material-owned packaging configuration; purchase lines retain packaging snapshots.';
        COMMENT ON TABLE product_recipes IS 'Product-to-raw-material association; deleting a product may remove recipe rows.';
        COMMENT ON TABLE product_option_groups IS 'Product-owned variant groups; order history stores independent snapshots.';
        COMMENT ON TABLE product_option_values IS 'Variant choices; order history stores independent name and price snapshots.';
        COMMENT ON TABLE product_discounts IS 'Mutable discount rules; applied discount history is stored separately.';
        COMMENT ON TABLE supplier_purchases IS 'Supplier purchase header; preserve purchase records and details.';
        COMMENT ON TABLE supplier_purchase_items IS 'Immutable purchase line snapshots; nullable master references are informational only.';
        COMMENT ON TABLE orders IS 'Immutable sale and receipt header. Do not hard-delete completed transactions through normal flows.';
        COMMENT ON TABLE order_items IS 'Immutable receipt line snapshots independent of current product master data.';
        COMMENT ON TABLE order_item_options IS 'Immutable variant snapshots for each purchased order line.';
        COMMENT ON TABLE order_item_discounts IS 'Immutable record of product discounts actually applied to an order line.';
        COMMENT ON TABLE order_discounts IS 'Immutable invoice-level discount snapshots.';
        COMMENT ON TABLE store_operation_settings IS 'Single-store operating configuration. Business timezone is configured globally as Asia/Jakarta (WIB).';
        COMMENT ON TABLE store_status_logs IS 'Append-only history of effective store open/close transitions.';
        COMMENT ON TABLE notifications IS 'Laravel polymorphic notifications; read_at NULL means unread. Enforce recipient ownership in application queries.';
        COMMENT ON TABLE personal_access_tokens IS 'Laravel Sanctum polymorphic API tokens; tokenable has no direct foreign key by design.';
        COMMENT ON COLUMN orders.store_name_snapshot IS 'Store display name copied at transaction creation; never derive historical receipts from current settings.';
        COMMENT ON COLUMN orders.cashier_name_snapshot IS 'Cashier display name copied at transaction time.';
        COMMENT ON COLUMN orders.transaction_time IS 'Transaction creation instant stored as timestamptz; display in Asia/Jakarta (WIB).';
        COMMENT ON COLUMN orders.paid_at IS 'Payment completion instant stored as timestamptz.';
        COMMENT ON COLUMN orders.receipt_issued_at IS 'Initial official receipt issue instant; preserve when reprinting.';
        COMMENT ON COLUMN order_items.product_id IS 'Nullable reference to current product; not the source of historical product name.';
        COMMENT ON COLUMN order_items.product_name_snapshot IS 'Authoritative product name for historical receipt.';
        COMMENT ON COLUMN order_items.variant_summary_snapshot IS 'Human-readable variant summary captured at transaction time.';
        COMMENT ON COLUMN order_items.final_subtotal IS 'Final order-line amount after applied line discounts.';
        COMMENT ON COLUMN order_item_options.option_value_id IS 'Nullable reference to mutable variant master; snapshot columns remain authoritative.';
        COMMENT ON COLUMN order_item_options.option_name_snapshot IS 'Authoritative option name for historical receipt.';
        COMMENT ON COLUMN order_item_discounts.discount_name_snapshot IS 'Name of discount as applied at transaction time.';
        COMMENT ON COLUMN supplier_purchase_items.item_name_snapshot IS 'Authoritative purchase item name when the purchase was recorded.';
        COMMENT ON COLUMN supplier_purchase_items.packaging_name_snapshot IS 'Authoritative packaging name when the purchase was recorded.';
        COMMENT ON COLUMN store_operation_settings.open_time IS 'Local business schedule time interpreted in Asia/Jakarta (WIB).';
        COMMENT ON COLUMN store_operation_settings.close_time IS 'Local business schedule time interpreted in Asia/Jakarta (WIB).';
        COMMENT ON COLUMN store_operation_settings.force_closed_until IS 'Absolute expiration instant for temporary forced closure.';
        COMMENT ON COLUMN store_status_logs.changed_at IS 'Absolute instant when the effective store state changed.';
        COMMENT ON COLUMN notifications.read_at IS 'NULL indicates unread; timestamp indicates when this recipient read the notification.';
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
        COMMENT ON COLUMN orders.store_name_snapshot IS NULL;
        COMMENT ON COLUMN orders.cashier_name_snapshot IS NULL;
        COMMENT ON COLUMN orders.transaction_time IS NULL;
        COMMENT ON COLUMN orders.paid_at IS NULL;
        COMMENT ON COLUMN orders.receipt_issued_at IS NULL;
        COMMENT ON COLUMN order_items.product_id IS NULL;
        COMMENT ON COLUMN order_items.product_name_snapshot IS NULL;
        COMMENT ON COLUMN order_items.variant_summary_snapshot IS NULL;
        COMMENT ON COLUMN order_items.final_subtotal IS NULL;
        COMMENT ON COLUMN order_item_options.option_value_id IS NULL;
        COMMENT ON COLUMN order_item_options.option_name_snapshot IS NULL;
        COMMENT ON COLUMN order_item_discounts.discount_name_snapshot IS NULL;
        COMMENT ON COLUMN supplier_purchase_items.item_name_snapshot IS NULL;
        COMMENT ON COLUMN supplier_purchase_items.packaging_name_snapshot IS NULL;
        COMMENT ON COLUMN store_operation_settings.open_time IS NULL;
        COMMENT ON COLUMN store_operation_settings.close_time IS NULL;
        COMMENT ON COLUMN store_operation_settings.force_closed_until IS NULL;
        COMMENT ON COLUMN store_status_logs.changed_at IS NULL;
        COMMENT ON COLUMN notifications.read_at IS NULL;
        COMMENT ON TABLE users IS NULL;
        COMMENT ON TABLE employees IS NULL;
        COMMENT ON TABLE employee_schedules IS NULL;
        COMMENT ON TABLE attendances IS NULL;
        COMMENT ON TABLE cashier_shifts IS NULL;
        COMMENT ON TABLE categories IS NULL;
        COMMENT ON TABLE raw_materials IS NULL;
        COMMENT ON TABLE products IS NULL;
        COMMENT ON TABLE product_packagings IS NULL;
        COMMENT ON TABLE raw_material_packagings IS NULL;
        COMMENT ON TABLE product_recipes IS NULL;
        COMMENT ON TABLE product_option_groups IS NULL;
        COMMENT ON TABLE product_option_values IS NULL;
        COMMENT ON TABLE product_discounts IS NULL;
        COMMENT ON TABLE supplier_purchases IS NULL;
        COMMENT ON TABLE supplier_purchase_items IS NULL;
        COMMENT ON TABLE orders IS NULL;
        COMMENT ON TABLE order_items IS NULL;
        COMMENT ON TABLE order_item_options IS NULL;
        COMMENT ON TABLE order_item_discounts IS NULL;
        COMMENT ON TABLE order_discounts IS NULL;
        COMMENT ON TABLE store_operation_settings IS NULL;
        COMMENT ON TABLE store_status_logs IS NULL;
        COMMENT ON TABLE notifications IS NULL;
        COMMENT ON TABLE personal_access_tokens IS NULL;
        SQL);
    }
};
