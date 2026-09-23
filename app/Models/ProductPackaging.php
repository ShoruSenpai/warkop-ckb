<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackaging extends Model
{
    protected $table = 'product_packagings';

    protected $fillable = [
        'product_id',
        'purchase_unit',
        'conversion_factor',
        'is_active',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
