<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'base_price',
        'stock',
        'stock_type',
        'image_url',
        'is_recommended',
        'status'
    ];

    protected $casts = [
        'stock' => 'integer',
        'base_price' => 'decimal:2',
        'is_recommended' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function packagings()
    {
        return $this->hasMany(ProductPackaging::class, 'product_id');
    }

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class, 'product_id');
    }
}
