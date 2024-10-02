<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Typification extends Model
{
    use HasFactory;
    protected $table = 'typifications';

    protected $fillable = [
        'id',
        'name',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'operator_id');
    }
}
