<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operators extends Model
{
    use HasFactory;

    protected $table = 'operators';

    protected $fillable = [
        'id',
        'name',
        'created_at'
    ];
    public function phoneNumbers()
    {
        return $this->hasMany(PhonesNumber::class, 'id');
    }
}
