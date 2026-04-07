<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlider extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hero_sliders';
    protected $fillable = [
        'title', 'subtitle', 'image_url', 'buttons', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'buttons' => 'array',
        'is_active' => 'boolean'
    ];
    
}
