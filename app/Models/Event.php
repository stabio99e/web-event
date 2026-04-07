<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventsLocation;
use App\Models\EventsTicketType;
use Mews\Purifier\Facades\Purifier;

class Event extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';
    protected $fillable = ['id', 'title', 'slug', 'description', 'content', 'image_path', 'start_datetime', 'end_datetime', 'max_attendees', 'is_active', 'url_group'];

    protected $keyType = 'int';
    public $incrementing = false;

    // Relationship to location
    public function EventsLocation()
    {
        return $this->hasOne(EventsLocation::class);
    }

    // Relationship to ticket types
    public function ticketTypes()
    {
        return $this->hasMany(EventsTicketType::class);
    }
    public function tickets()
    {
        return $this->hasManyThrough(
            \App\Models\Ticket::class,
            \App\Models\OrderItem::class,
            'order_id', // Foreign key on order_items table...
            'order_item_id', // Foreign key on tickets table...
            'id', // Local key on events table...
            'id', // Local key on order_items table...
        )->whereHas('order', function ($query) {
            $query->where('event_id', $this->id);
        });
    }

    // Relationship to certificate template
    public function certificateTemplate()
    {
        return $this->hasOne(CertificateTemplate::class);
    }

    // Clean up HTML content before saving
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value, [
            'HTML.Allowed' => 'p,br,strong,em,ul,ol,li,a[href|title],h1,h2,h3,blockquote',
            'HTML.AllowedAttributes' => 'a.href,a.title,img.src,img.alt',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
        ]);
    }

    // Accessor for purified content
    public function getSafeContentAttribute()
    {
        return nl2br($this->content);
    }
}
