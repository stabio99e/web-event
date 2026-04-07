<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }

    public function index(Request $request)
    {
        $userID = Auth::id();
        $query = User::where('id', '!=', $userID);
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        $getUsersALL = $query->latest()->paginate(15);

        return view('admin.user.index', compact('getUsersALL'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'saldo' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($id);
        $user->saldo = $request->saldo;
        $user->save();

        return redirect()->route('admin.user.show')->with('success', 'Saldo berhasil diperbarui.');
    }
}
