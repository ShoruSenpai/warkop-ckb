<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDiscount extends Model
{
    protected $table = "order_discounts";

    public $timestamps = false;

    protected $fillable = [
        "order_id",
        "discount_name_snapshot",
        "discount_type_snapshot",
        "discount_value_snapshot",
        "discount_amount",
        "created_at",
    ];

    protected $casts = [
        "order_id" => "integer",
        "discount_value_snapshot" => "decimal:2",
        "discount_amount" => "integer",
        "created_at" => "immutable_datetime",
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, "order_id");
    }
}
