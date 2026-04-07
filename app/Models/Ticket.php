<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number', 'order_item_id', 'user_id', 
        'attendee_name', 'attendee_email', 'attendance_status', 'attendance_note', 'checkin_time'
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getQrCodeBase64()
    {
        $png = QrCode::format('png')->size(200)->generate($this->ticket_number);
        return 'data:image/png;base64,' . base64_encode($png);
    }
}