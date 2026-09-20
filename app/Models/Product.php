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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
