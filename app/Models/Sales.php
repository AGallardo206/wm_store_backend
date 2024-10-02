<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'id',
        'user_id',
        'sales_user_id',
        'customer_id',
        'origin',
        'sales_order',
        'notes',
        'typification_id',
        'operator_id',
        'phone',
        'equip',
        'imei',
        'sales_type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesUser()
    {
        return $this->belongsTo(SalesUser::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function typification()
    {
        return $this->belongsTo(Typification::class);
    }

    public function phoneNumbers()
    {
        return $this->belongsTo(PhonesNumber::class); // Asumiendo que hay una relación
    }
}
