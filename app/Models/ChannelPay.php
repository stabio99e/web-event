<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelPay extends Model
{
    protected $table = 'channel_pay';

    protected $fillable = ['channel_name', 'channel_code', 'channel_group', 'type', 'biaya_flat', 'biaya_percent', 'ppn', 'status'];
}
