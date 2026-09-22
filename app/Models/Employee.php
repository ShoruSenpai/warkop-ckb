<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'employee_code',
        'full_name',
        'position',
        'phone_number',
        'is_active'
    ];

    protected $hidden = [
        'pin_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
