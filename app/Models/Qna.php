<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class Qna extends Model
{
    protected $table = 'qnas';
    protected $fillable = [
        'question',
        'answer',
    ];

    // Clean up HTML answer before saving
    public function setContentAttribute($value)
    {
        $this->attributes['answer'] = Purifier::clean($value, [
            'HTML.Allowed' => 'p,br,strong,em,ul,ol,li,a[href],img[src|alt],h1,h2,h3,blockquote',
        ]);        
    }

    // Accessor for purified answer
    public function getSafeContentAttribute()
    {
        return nl2br($this->answer);
    }
}
