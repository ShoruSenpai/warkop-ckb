<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItem extends Model
{
    public $table = 'supplier_purchase_items';

    protected $fillable = [
        'purchase_id',
        'raw_material_id',
        'product_id',
        'purchase_unit',
        'quantity',
        'conversion_factor',
        'unit_price',
        'subtotal'
    ];

    public $timestamps = false;

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
