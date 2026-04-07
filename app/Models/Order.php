<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['id', 'user_id', 'event_id', 'order_number', 'TotalPayAmount', 'total_amount', 'admin_fee', 'ppn_fee', 'status', 'payment_method', 'paid_at'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    protected $keyType = 'int';
    public $incrementing = false;

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, OrderItem::class);
    }
    public function getGrandTotalAttribute()
    {
        return $this->total_amount + $this->admin_fee;
    }
}
