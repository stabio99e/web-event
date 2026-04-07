<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events_locations';
    protected $fillable = ['event_id', 'name', 'address', 'city', 'province', 'country', 'map_url'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
