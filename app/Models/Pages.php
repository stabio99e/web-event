<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class Pages extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages';
    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'is_published', 'published_at', 'order'];

    // Clean up HTML content before saving
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value, [
            'HTML.Allowed' => 'p,br,strong,em,ul,ol,li,a[href],img[src|alt],h1,h2,h3,blockquote',
        ]);        
    }

    // Accessor for purified content
    public function getSafeContentAttribute()
    {
        return nl2br($this->content);
    }
}
