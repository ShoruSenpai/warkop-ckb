<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemDiscount extends Model
{
    protected $table = "order_item_discounts";

    public $timestamps = false;

    protected $fillable = [
        "order_item_id",
        "product_discount_id",
        "discount_name_snapshot",
        "discount_type_snapshot",
        "discount_value_snapshot",
        "discount_amount",
        "created_at",
    ];

    protected $casts = [
        "order_item_id" => "integer",
        "product_discount_id" => "integer",
        "discount_value_snapshot" => "decimal:2",
        "discount_amount" => "integer",
        "created_at" => "immutable_datetime",
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, "order_item_id");
    }
}
