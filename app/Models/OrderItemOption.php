<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemOption extends Model
{
    protected $table = "order_item_options";

    public $timestamps = false;

    protected $fillable = [
        "order_item_id",
        "option_value_id",
        "option_group_name_snapshot",
        "option_name_snapshot",
        "extra_price_snapshot",
    ];

    protected $casts = [
        "order_item_id" => "integer",
        "option_value_id" => "integer",
        "extra_price_snapshot" => "integer",
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, "order_item_id");
    }
}
