<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = "orders";

    protected $fillable = [
        "invoice_code",
        "cashier_shift_id",
        "cashier_id",
        "cashier_name_snapshot",
        "store_name_snapshot",
        "status",
        "order_type",
        "gross_amount",
        "discount_amount",
        "final_amount",
        "payment_method",
        "payment_status",
        "transaction_time",
        "paid_at",
        "receipt_issued_at",
    ];

    protected $casts = [
        "cashier_shift_id" => "integer",
        "cashier_id" => "integer",
        "gross_amount" => "integer",
        "discount_amount" => "integer",
        "final_amount" => "integer",
        "transaction_time" => "immutable_datetime",
        "paid_at" => "immutable_datetime",
        "receipt_issued_at" => "immutable_datetime",
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, "order_id");
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(OrderDiscount::class, "order_id");
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(Employee::class, "cashier_id");
    }
}
