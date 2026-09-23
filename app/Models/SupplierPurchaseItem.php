<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItem extends Model
{
    public $table = 'supplier_purchase_items';

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',

        'raw_material_id',
        'raw_material_packaging_id',

        'product_id',
        'product_packaging_id',

        'purchase_unit',
        'quantity',
        'conversion_factor',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'conversion_factor' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function rawMaterialPackaging()
    {
        return $this->belongsTo(RawMaterialPackaging::class, 'raw_material_packaging_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productPackaging()
    {
        return $this->belongsTo(ProductPackaging::class, 'product_packaging_id');
    }
}
