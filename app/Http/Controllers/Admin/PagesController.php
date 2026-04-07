<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pages;
use Illuminate\Support\Str;

class PagesController extends Controller
{
    public function index()
    {
        $getPages = Pages::orderBy('order', 'asc')->get();
        return view('admin.pages.index', compact('getPages'));
    }
    public function create()
    {
        return view('admin.pages.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:1',
        ]);

        $order = $request->order ?? 1;

        Pages::where('order', '>=', $order)->increment('order');

        Pages::create([
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'order' => $order,
            'is_published' => 1,
        ]);

        return redirect()->route('admin.pages.show')->with('success', 'Page created successfully with proper order.');
    }

    public function edit($id)
    {
        $page = Pages::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
        ]);

        $page = Pages::findOrFail($id);
        $newOrder = $request->order ?? 0;

        if ($newOrder && $newOrder != $page->order) {
            Pages::where('id', '!=', $page->id)->where('order', '>=', $newOrder)->increment('order');
        }

        $page->update([
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'order' => $newOrder,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.pages.show')->with('success', 'Page updated with order adjusted.');
    }
    public function destroy($id)
    {
        $page = Pages::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.show')->with('success', 'Halaman berhasil dihapus.');
    }
}
