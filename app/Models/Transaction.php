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
        'cvv',
        'quantity',
        'card_number',
        'product_id',
        'payment_method',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
}
