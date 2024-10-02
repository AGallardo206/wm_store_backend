<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $table = 'agencies';

    protected $fillable = [
        'id',
        'name',
        'address',
        'phone',
        'email',
    ];

    public function salesUser()
    {
        return $this->hasMany(SalesUser::class);
    }
}
