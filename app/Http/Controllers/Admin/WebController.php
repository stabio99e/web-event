<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }

    public function index()
    {
        $webConfig = WebConfig::first();
        return view('admin.config.index', compact('webConfig'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string',
            'site_description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_whatsapp' => 'nullable|string',
            'logo_path' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon_path' => 'nullable|image|mimes:png,ico,jpg,jpeg|max:1024',
        ]);
    
        $config = WebConfig::first();
        if (!$config) {
            $config = new WebConfig();  
        }
    
        // Handle logo upload
        if ($request->hasFile('logo_path')) {
            if ($config->logo_path) {
                $oldLogoPath = public_path($config->logo_path);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
    
            $logo = $request->file('logo_path')->store('images', 'public');
            $config->logo_path = '/storage/' . $logo;
        }
    
        // Handle favicon upload
        if ($request->hasFile('favicon_path')) {
            if ($config->favicon_path) {
                $oldFaviconPath = public_path($config->favicon_path);
                if (file_exists($oldFaviconPath)) {
                    unlink($oldFaviconPath);
                }
            }
    
            $favicon = $request->file('favicon_path')->store('images', 'public');
            $config->favicon_path = '/storage/' . $favicon;
        }
    
        // Simpan semua data lainnya
        $config->site_name = $request->site_name;
        $config->site_tagline = $request->site_tagline;
        $config->site_description = $request->site_description;
        $config->contact_email = $request->contact_email;
        $config->contact_whatsapp = $request->contact_whatsapp;
    
        $config->save();
    
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}    