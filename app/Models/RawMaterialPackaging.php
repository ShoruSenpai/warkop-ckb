<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPackaging extends Model
{
    protected $table = 'raw_material_packagings';

    protected $fillable = [
        'raw_material_id',
        'purchase_unit',
        'conversion_factor',
        'is_active'
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:3',
        'is_active' => 'boolean'
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }
}
