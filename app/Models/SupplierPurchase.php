<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchase extends Model
{
    protected $table = 'supplier_purchases';

    protected $fillable = [
        'invoice_number',
        'supplier_name',
        'total_cost',
        'purchase_date',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(SupplierPurchaseItem::class, 'purchase_id');
    }
}
