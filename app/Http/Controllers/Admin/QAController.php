<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Qna;

class QAController extends Controller
{
    public function index()
    {
        $qnas = Qna::latest()->get();
        return view('admin.qnas.index', compact('qnas'));
    }

    public function create()
    {
        return view('admin.qnas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        Qna::create($request->only('question', 'answer'));

        return redirect()->route('admin.qnas.show')->with('success', 'Q&A berhasil ditambahkan.');
    }

    public function edit(Qna $qna)
    {
        return view('admin.qnas.edit', compact('qna'));
    }

    public function update(Request $request, Qna $qna)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $qna->update($request->only('question', 'answer'));

        return redirect()->route('admin.qnas.show')->with('success', 'Q&A berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $qna = Qna::findOrFail($id);
        $qna->delete();

        return redirect()->route('admin.qnas.show')->with('success', 'Q&A berhasil dihapus.');
    }
}
