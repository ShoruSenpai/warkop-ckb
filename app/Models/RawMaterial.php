<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $table = 'raw_materials';

    protected $fillable = [
        'name',
        'unit_measurement',
        'current_stock',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2'
    ];

    public function packagings()
    {
        return $this->hasMany(RawMaterialPackaging::class, 'raw_material_id'
        );
    }

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class, 'raw_material_id'
        );
    }
}
