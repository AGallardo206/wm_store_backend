<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRecord extends Model
{
    use HasFactory;

    protected $table = 'customer_records';

    protected $fillable = [
        'operator_id',
        'phone',
        'schedule_1',
        'schedule_2',
        'schedule_3',
        'user_id',
        'customer_id',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operators::class);
    }

    public function phoneNumbers()
    {
        return $this->belongsTo(PhonesNumber::class); // Asumiendo que hay una relación
    }
}
