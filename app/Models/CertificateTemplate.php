<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $table = 'certificate_templates';
    protected $fillable = ['event_id', 'name', 'image_path', 'font_path', 'font_size', 'position_x', 'position_y', 'text_color', 'is_active'];

    // Relationship to event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
