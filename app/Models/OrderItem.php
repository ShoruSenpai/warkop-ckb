<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $table = "order_items";

    public $timestamps = false;

    protected $fillable = [
        "order_id",
        "product_id",
        "product_name_snapshot",
        "variant_summary_snapshot",
        "quantity",
        "unit_price",
        "option_price",
        "subtotal",
        "discount_amount",
        "final_subtotal",
    ];

    protected $casts = [
        "order_id" => "integer",
        "product_id" => "integer",
        "quantity" => "integer",
        "unit_price" => "integer",
        "option_price" => "integer",
        "subtotal" => "integer",
        "discount_amount" => "integer",
        "final_subtotal" => "integer",
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, "order_id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class, "order_item_id");
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(OrderItemDiscount::class, "order_item_id");
    }
}
