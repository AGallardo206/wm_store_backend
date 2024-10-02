<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonesNumber extends Model
{
    use HasFactory;

    protected $table = 'phones_numbers';

    protected $fillable = [
        'id',
        'phone',
        'created_at',
        'operator_id',
        'customer_id'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function operator()
    {
        return $this->belongsTo(Operators::class, 'operator_id');
    }

    public function sales() {
        return $this->hasMany(Sales::class);
    }

    public function customerRecords()
    {
        return $this->belongsTo(CustomerRecord::class);
    }
}
