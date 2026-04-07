<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsTicketType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events_ticket_types';
    protected $fillable = ['event_id', 'name', 'description', 'price', 'quantity_available', 'is_premium', 'is_active'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function tickets()
    {
        return $this->hasManyThrough(
            \App\Models\Ticket::class,
            \App\Models\OrderItem::class,
            'ticket_type_id', // Foreign key on OrderItem
            'order_item_id', // Foreign key on Ticket
            'id', // Local key on EventsTicketType
            'id', // Local key on OrderItem
        );
    }
}
