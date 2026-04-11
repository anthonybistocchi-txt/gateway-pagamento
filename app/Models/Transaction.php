<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'gateway_id',
        'amount',
        'status',
        'external_id',
        'quantity',
        'card_number',
        'product_id',
        'payment_method',
        'payment_key',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
