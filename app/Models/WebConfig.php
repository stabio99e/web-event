<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebConfig extends Model
{
    protected $table = 'web_config';
    protected $fillable = ['site_name', 'site_tagline', 'site_description', 'contact_email', 'contact_whatsapp', 'logo_path', 'favicon_path', 'meta_title', 'meta_description', 'meta_keywords'];
}
