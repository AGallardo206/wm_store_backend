<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesUser extends Model
{
    use HasFactory;

    protected $table = 'sales_users';

    protected $fillable = [
        'name',
        'created_at',
        'agency_id'
    ];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
