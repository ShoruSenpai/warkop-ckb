<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    protected $table = 'product_recipes';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'raw_material_id',
        'amount_needed'
    ];

    protected $casts = [
        'amount_needed' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }
}
