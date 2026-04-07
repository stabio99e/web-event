<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id', 'reference', 'merchant_ref', 'payment_method', 'payment_name',
        'customer_name', 'customer_email', 'customer_phone', 'pay_code',
        'checkout_url', 'status', 'amount', 'fee_merchant', 'fee_customer',
        'amount_received', 'expired_time', 'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'expired_time' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
