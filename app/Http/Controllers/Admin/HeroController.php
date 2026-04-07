<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeroSlider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HeroController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }
    public function index()
    {
        $heros = HeroSlider::orderBy('sort_order')->get();
        return view('admin.hero.index', compact('heros'));
    }

    public function create()
    {
        return view('admin.hero.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'buttons' => 'nullable|array',
            'buttons.*.icon' => 'required|string|in:instagram,facebook,youtube,tiktok',
            'buttons.*.label' => 'nullable|string|max:50',
            'buttons.*.link' => 'required|url',
            'is_active' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $filename = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('storage/hero'), $filename);
            $data['image_url'] = 'storage/hero/' . $filename;
        }

        $data['buttons'] = $request->buttons;

        $maxSortOrder = HeroSlider::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;
        HeroSlider::create([
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'buttons' => $data['buttons'] ?? [],
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'],
        ]);

        return redirect()->route('admin.hero.show')->with('success', 'Hero slider berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $slider = HeroSlider::findOrFail($id);
        return view('admin.hero.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = HeroSlider::findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'buttons' => 'nullable|array',
            'buttons.*.icon' => 'required|string|in:instagram,facebook,youtube,tiktok',
            'buttons.*.label' => 'nullable|string|max:50',
            'buttons.*.link' => 'required|url',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle image update
        if ($request->hasFile('image')) {
            if ($slider->image_url && file_exists(public_path($slider->image_url))) {
                @unlink(public_path($slider->image_url));
            }

            $filename = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('storage/hero'), $filename);
            $data['image_url'] = 'storage/hero/' . $filename;
        }

        // Auto set sort_order if not provided
        if (is_null($request->sort_order)) {
            $maxSort = HeroSlider::where('id', '!=', $slider->id)->max('sort_order');
            $data['sort_order'] = $maxSort ? $maxSort + 1 : 1;
        }

        // Save buttons as array (thanks to casts)
        $data['buttons'] = $request->buttons ?? [];

        $slider->update($data);

        return redirect()->route('admin.hero.show')->with('success', 'Hero slider berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $slider = HeroSlider::findOrFail($id);
        if (file_exists(public_path($slider->image_url))) {
            unlink(public_path($slider->image_url));
        }
        $slider->delete();
        return redirect()->route('admin.hero.show')->with('success', 'Hero slider berhasil dihapus.');
    }
}
