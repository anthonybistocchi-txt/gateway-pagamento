<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'amount',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
}
