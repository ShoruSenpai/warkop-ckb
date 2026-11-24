<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderHistorySeeder extends Seeder
{
    private const STORE_NAME = 'Warkop Cak Kebo';

    private const STARTING_CASH = 100000;

    /**
     * Create 20 demo dine-in orders for sales-report testing.
     *
     * - 15 completed orders with paid payments.
     * - 5 cancelled orders with cancelled payments and no paid_at timestamp.
     * - Every order is connected to an employee and a cashier shift.
     * - Order items and selected product options are stored as snapshots.
     *
     * The seeder is idempotent: fixed invoice codes prevent duplicate orders.
     * It does not decrease product stock because these are historical records.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException(
                'OrderHistorySeeder is for development/testing only.',
            );
        }

        $definitions = $this->orderDefinitions();

        DB::transaction(function () use ($definitions): void {
            $cashiers = DB::table('employees')
                ->whereRaw('LOWER(BTRIM(position)) = ?', ['cashier'])
                ->where('is_active', true)
                ->orderBy('id')
                ->limit(2)
                ->get(['id', 'full_name']);

            if ($cashiers->count() < 2) {
                throw new RuntimeException(
                    'Two active cashier employees are required. Run UserRoleSeeder first.',
                );
            }

            $products = $this->loadProducts();
            $optionValues = $this->loadOptionValues();
            $timezone = config('business.timezone', 'Asia/Jakarta');
            $shiftIds = [];

            foreach ($definitions as $index => $definition) {
                $invoiceCode = sprintf('SEED-SALES-%03d', $index + 1);

                // Keep existing demo records untouched when the seeder is rerun.
                $existingOrder = DB::table('orders')
                    ->where('invoice_code', $invoiceCode)
                    ->first();

                $slotInDay = $index % 4;
                $cashier = $slotInDay < 2
                    ? $cashiers->get(0)
                    : $cashiers->get(1);

                $transactionTime = CarbonImmutable::now($timezone)
                    ->subDays($definition['days_ago'])
                    ->setTimeFromTimeString($definition['time']);

                $shiftKey = $cashier->id . '|' . $transactionTime->toDateString();

                if ($existingOrder) {
                    $shiftIds[$shiftKey] = (int) $existingOrder->cashier_shift_id;
                    continue;
                }

                if (!isset($shiftIds[$shiftKey])) {
                    $shiftIds[$shiftKey] = $this->getOrCreateShift(
                        (int) $cashier->id,
                        $transactionTime,
                    );
                }

                $orderItems = $this->buildOrderItems(
                    $definition['items'],
                    $products,
                    $optionValues,
                );

                $grossAmount = array_sum(array_column($orderItems, 'subtotal'));
                $discountAmount = 0;
                $finalAmount = $grossAmount - $discountAmount;
                $isCancelled = $definition['status'] === 'cancelled';
                $paidAt = $isCancelled
                    ? null
                    : $transactionTime->addMinutes(5);
                $receiptIssuedAt = $paidAt?->addMinute();
                $now = CarbonImmutable::now($timezone);

                $orderId = (int) DB::table('orders')->insertGetId([
                    'invoice_code' => $invoiceCode,
                    'cashier_shift_id' => $shiftIds[$shiftKey],
                    'cashier_id' => $cashier->id,
                    'cashier_name_snapshot' => $cashier->full_name,
                    'store_name_snapshot' => self::STORE_NAME,
                    'status' => $definition['status'],
                    'order_type' => 'dinein',
                    'gross_amount' => $grossAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'payment_method' => $definition['payment_method'],
                    'payment_status' => $isCancelled ? 'cancelled' : 'paid',
                    'transaction_time' => $transactionTime->format('Y-m-d H:i:sP'),
                    'paid_at' => $paidAt?->format('Y-m-d H:i:sP'),
                    'receipt_issued_at' => $receiptIssuedAt?->format('Y-m-d H:i:sP'),
                    'created_at' => $transactionTime->format('Y-m-d H:i:sP'),
                    'updated_at' => $now->format('Y-m-d H:i:sP'),
                ]);

                foreach ($orderItems as $item) {
                    $itemId = (int) DB::table('order_items')->insertGetId([
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'product_name_snapshot' => $item['product_name_snapshot'],
                        'variant_summary_snapshot' => $item['variant_summary_snapshot'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'option_price' => $item['option_price'],
                        'subtotal' => $item['subtotal'],
                        'discount_amount' => 0,
                        'final_subtotal' => $item['subtotal'],
                    ]);

                    foreach ($item['options'] as $option) {
                        DB::table('order_item_options')->insert([
                            'order_item_id' => $itemId,
                            'option_value_id' => $option['option_value_id'],
                            'option_group_name_snapshot' => $option['group_name'],
                            'option_name_snapshot' => $option['option_name'],
                            'extra_price_snapshot' => $option['extra_price'],
                        ]);
                    }
                }
            }

            // Recalculate demo-shift totals from completed, paid orders only.
            foreach (array_unique($shiftIds) as $shiftId) {
                $shift = DB::table('cashier_shifts')
                    ->where('id', $shiftId)
                    ->first(['starting_cash']);

                if (!$shift) {
                    continue;
                }

                $paidOrders = DB::table('orders')
                    ->where('cashier_shift_id', $shiftId)
                    ->where('status', 'completed')
                    ->where('payment_status', 'paid')
                    ->get(['payment_method', 'final_amount']);

                $cashSales = 0;
                $qrisSales = 0;

                foreach ($paidOrders as $order) {
                    if ($order->payment_method === 'cash') {
                        $cashSales += (int) $order->final_amount;
                    } elseif ($order->payment_method === 'qris') {
                        $qrisSales += (int) $order->final_amount;
                    }
                }

                DB::table('cashier_shifts')
                    ->where('id', $shiftId)
                    ->update([
                        'ending_cash' => (int) $shift->starting_cash + $cashSales,
                        'total_sales_cash' => $cashSales,
                        'total_sales_qris' => $qrisSales,
                        'total_sales_transfer' => 0,
                        'updated_at' => CarbonImmutable::now($timezone)
                            ->format('Y-m-d H:i:sP'),
                    ]);
            }
        });
    }

    /**
     * Load products by their seeded names so snapshots use current product IDs
     * and prices instead of assuming fixed primary keys.
     */
    private function loadProducts(): array
    {
        $requiredNames = [
            'Es Kopi Susu',
            'Americano',
            'Matcha Latte',
            'Chocolate Milk',
            'Fresh Milk',
            'Susu Gula Aren',
            'Mie Goreng Telur',
            'Nasi Goreng Telur',
            'French Fries',
            'Roti Bakar Cokelat',
            'Rokok Filter 12',
            'Rokok Kretek 12',
        ];

        $products = DB::table('products')
            ->whereIn('name', $requiredNames)
            ->get(['id', 'name', 'base_price'])
            ->keyBy('name')
            ->all();

        $missingProducts = array_values(array_diff($requiredNames, array_keys($products)));

        if ($missingProducts !== []) {
            throw new RuntimeException(
                'Missing products. Run ProductSeeder first: ' . implode(', ', $missingProducts),
            );
        }

        return $products;
    }

    /**
     * Map product option values using product, group, and option names.
     */
    private function loadOptionValues(): array
    {
        $rows = DB::table('product_option_values as pov')
            ->join(
                'product_option_groups as pog',
                'pog.id',
                '=',
                'pov.option_group_id',
            )
            ->get([
                'pog.product_id',
                'pog.group_name',
                'pov.id',
                'pov.option_name',
                'pov.extra_price',
            ]);

        $optionValues = [];

        foreach ($rows as $row) {
            $key = $row->product_id . '|' . $row->group_name . '|' . $row->option_name;
            $optionValues[$key] = $row;
        }

        return $optionValues;
    }

    /**
     * Create one closed demo shift per cashier per seeded business day.
     */
    private function getOrCreateShift(
        int $cashierId,
        CarbonImmutable $transactionTime,
    ): int {
        $shiftStart = $transactionTime->setTime(7, 55);
        $shiftEnd = $transactionTime->setTime(22, 5);

        $existingShift = DB::table('cashier_shifts')
            ->where('cashier_id', $cashierId)
            ->whereRaw('start_time = ?::timestamptz', [
                $shiftStart->format('Y-m-d H:i:sP'),
            ])
            ->first(['id']);

        if ($existingShift) {
            return (int) $existingShift->id;
        }

        return (int) DB::table('cashier_shifts')->insertGetId([
            'cashier_id' => $cashierId,
            'start_time' => $shiftStart->format('Y-m-d H:i:sP'),
            'end_time' => $shiftEnd->format('Y-m-d H:i:sP'),
            'starting_cash' => self::STARTING_CASH,
            'ending_cash' => self::STARTING_CASH,
            'total_sales_cash' => 0,
            'total_sales_qris' => 0,
            'total_sales_transfer' => 0,
            'status' => 'closed',
            'created_at' => $shiftStart->format('Y-m-d H:i:sP'),
            'updated_at' => $shiftEnd->format('Y-m-d H:i:sP'),
        ]);
    }

    /**
     * Build item rows and resolve option IDs from ProductSeeder's data.
     */
    private function buildOrderItems(
        array $definitions,
        array $products,
        array $optionValues,
    ): array {
        $items = [];

        foreach ($definitions as $definition) {
            $product = $products[$definition['product']] ?? null;

            if (!$product) {
                throw new RuntimeException(
                    "Product {$definition['product']} was not found.",
                );
            }

            $quantity = (int) $definition['quantity'];
            $unitPrice = (int) $product->base_price;
            $selectedOptions = [];
            $optionPrice = 0;
            $variantParts = [];

            foreach ($definition['options'] ?? [] as $groupName => $optionName) {
                $key = $product->id . '|' . $groupName . '|' . $optionName;
                $option = $optionValues[$key] ?? null;

                if (!$option) {
                    throw new RuntimeException(
                        "Option {$groupName}: {$optionName} for {$product->name} was not found. " .
                        'Run ProductSeeder first or check the option definitions.',
                    );
                }

                $extraPrice = (int) $option->extra_price;
                $optionPrice += $extraPrice;
                $variantParts[] = $groupName . ': ' . $optionName;
                $selectedOptions[] = [
                    'option_value_id' => (int) $option->id,
                    'group_name' => $groupName,
                    'option_name' => $optionName,
                    'extra_price' => $extraPrice,
                ];
            }

            // Option price is a per-unit add-on, so it is multiplied by quantity.
            $subtotal = ($unitPrice + $optionPrice) * $quantity;

            $items[] = [
                'product_id' => (int) $product->id,
                'product_name_snapshot' => $product->name,
                'variant_summary_snapshot' => $variantParts === []
                    ? null
                    : implode(', ', $variantParts),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'option_price' => $optionPrice,
                'subtotal' => $subtotal,
                'options' => $selectedOptions,
            ];
        }

        return $items;
    }

    /**
     * 20 orders spread over five recent business days, four orders per day.
     * The first two orders use cashier #1; the next two use cashier #2.
     */
    private function orderDefinitions(): array
    {
        return [
            // Day 1
            [
                'days_ago' => 1,
                'time' => '09:15:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Es Kopi Susu', 'quantity' => 2, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                    ['product' => 'Mie Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Pedas']],
                    ['product' => 'French Fries', 'quantity' => 1, 'options' => ['Bumbu' => 'BBQ']],
                ],
            ],
            [
                'days_ago' => 1,
                'time' => '11:20:00',
                'status' => 'cancelled',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Americano', 'quantity' => 1, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Less Sugar', 'Es' => 'No Ice']],
                    ['product' => 'Roti Bakar Cokelat', 'quantity' => 1],
                ],
            ],
            [
                'days_ago' => 1,
                'time' => '13:05:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Matcha Latte', 'quantity' => 2, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Less Sugar', 'Es' => 'Less Ice']],
                    ['product' => 'Nasi Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Sedang']],
                ],
            ],
            [
                'days_ago' => 1,
                'time' => '16:40:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Chocolate Milk', 'quantity' => 1, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Normal', 'Es' => 'Less Ice']],
                    ['product' => 'Fresh Milk', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'No Sugar', 'Es' => 'No Ice']],
                ],
            ],

            // Day 2
            [
                'days_ago' => 2,
                'time' => '09:15:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Susu Gula Aren', 'quantity' => 2, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                    ['product' => 'French Fries', 'quantity' => 2, 'options' => ['Bumbu' => 'Balado']],
                ],
            ],
            [
                'days_ago' => 2,
                'time' => '11:20:00',
                'status' => 'cancelled',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Mie Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Tidak Pedas']],
                    ['product' => 'Es Kopi Susu', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Less Sugar', 'Es' => 'Normal Ice']],
                ],
            ],
            [
                'days_ago' => 2,
                'time' => '13:05:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Nasi Goreng Telur', 'quantity' => 2, 'options' => ['Level Pedas' => 'Pedas']],
                    ['product' => 'Chocolate Milk', 'quantity' => 1, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                ],
            ],
            [
                'days_ago' => 2,
                'time' => '16:40:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'French Fries', 'quantity' => 2, 'options' => ['Bumbu' => 'Original']],
                    ['product' => 'Americano', 'quantity' => 2, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'No Sugar', 'Es' => 'No Ice']],
                ],
            ],

            // Day 3
            [
                'days_ago' => 3,
                'time' => '09:15:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Roti Bakar Cokelat', 'quantity' => 1],
                    ['product' => 'Matcha Latte', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Normal', 'Es' => 'Less Ice']],
                ],
            ],
            [
                'days_ago' => 3,
                'time' => '11:20:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Fresh Milk', 'quantity' => 2, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Less Sugar', 'Es' => 'Normal Ice']],
                    ['product' => 'Mie Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Sedang']],
                ],
            ],
            [
                'days_ago' => 3,
                'time' => '13:05:00',
                'status' => 'cancelled',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Susu Gula Aren', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                    ['product' => 'French Fries', 'quantity' => 1, 'options' => ['Bumbu' => 'BBQ']],
                    ['product' => 'Rokok Filter 12', 'quantity' => 1],
                ],
            ],
            [
                'days_ago' => 3,
                'time' => '16:40:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Rokok Kretek 12', 'quantity' => 1],
                    ['product' => 'Americano', 'quantity' => 1, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                ],
            ],

            // Day 4
            [
                'days_ago' => 5,
                'time' => '09:15:00',
                'status' => 'cancelled',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Es Kopi Susu', 'quantity' => 2, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                    ['product' => 'Nasi Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Tidak Pedas']],
                ],
            ],
            [
                'days_ago' => 5,
                'time' => '11:20:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Matcha Latte', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Less Sugar', 'Es' => 'Less Ice']],
                    ['product' => 'Roti Bakar Cokelat', 'quantity' => 2],
                ],
            ],
            [
                'days_ago' => 5,
                'time' => '13:05:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Chocolate Milk', 'quantity' => 2, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                    ['product' => 'French Fries', 'quantity' => 1, 'options' => ['Bumbu' => 'Original']],
                ],
            ],
            [
                'days_ago' => 5,
                'time' => '16:40:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Mie Goreng Telur', 'quantity' => 2, 'options' => ['Level Pedas' => 'Pedas']],
                    ['product' => 'Rokok Filter 12', 'quantity' => 1],
                ],
            ],

            // Day 5
            [
                'days_ago' => 7,
                'time' => '09:15:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Americano', 'quantity' => 1, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'No Sugar', 'Es' => 'Normal Ice']],
                    ['product' => 'Fresh Milk', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Less Sugar', 'Es' => 'No Ice']],
                    ['product' => 'Roti Bakar Cokelat', 'quantity' => 1],
                ],
            ],
            [
                'days_ago' => 7,
                'time' => '11:20:00',
                'status' => 'completed',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Nasi Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Sedang']],
                    ['product' => 'Susu Gula Aren', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Normal', 'Es' => 'Normal Ice']],
                    ['product' => 'French Fries', 'quantity' => 1, 'options' => ['Bumbu' => 'Balado']],
                ],
            ],
            [
                'days_ago' => 7,
                'time' => '13:05:00',
                'status' => 'completed',
                'payment_method' => 'qris',
                'items' => [
                    ['product' => 'Es Kopi Susu', 'quantity' => 1, 'options' => ['Ukuran' => 'Large', 'Level Gula' => 'Normal', 'Es' => 'Less Ice']],
                    ['product' => 'Chocolate Milk', 'quantity' => 1, 'options' => ['Ukuran' => 'Regular', 'Level Gula' => 'Less Sugar', 'Es' => 'Normal Ice']],
                ],
            ],
            [
                'days_ago' => 7,
                'time' => '16:40:00',
                'status' => 'cancelled',
                'payment_method' => 'cash',
                'items' => [
                    ['product' => 'Rokok Kretek 12', 'quantity' => 2],
                    ['product' => 'Mie Goreng Telur', 'quantity' => 1, 'options' => ['Level Pedas' => 'Tidak Pedas']],
                ],
            ],
        ];
    }
}
