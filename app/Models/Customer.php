<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'id',
        'agency_id',
        'name',
        'dni',
        'created_at'
    ];
    public function phoneNumbers()
    {
        return $this->hasMany(PhonesNumber::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function customerRecords()
    {
        return $this->hasMany(CustomerRecord::class);
    }
}
