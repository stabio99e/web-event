<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChannelPay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CHPayController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }

    public function index()
    {
        $getChannel = ChannelPay::all();
        return view('admin.channel.index', compact('getChannel'));
    }

    public function create()
    {
        return view('admin.channel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'channel_name' => 'required',
            'channel_code' => 'required',
            'channel_group' => 'required',
            'type' => 'required',
            'biaya_flat' => 'required|numeric',
            'biaya_percent' => 'nullable|numeric',
            'ppn' => 'required|numeric',
        ]);

        ChannelPay::create($request->all());
        return redirect()->route('admin.channel.CHPay')->with('success', 'Channel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $channel = ChannelPay::findOrFail($id);
        return view('admin.channel.edit', compact('channel'));
    }

    public function update(Request $request, $id)
    {
        $channel = ChannelPay::findOrFail($id);

        $request->validate([
            'channel_name' => 'required',
            'channel_code' => 'required',
            'channel_group' => 'required|in:VA,Ewallet,Qris',
            'type' => 'required|in:DIRECT,REDIRECT',
            'biaya_flat' => 'required|numeric',
            'biaya_percent' => 'nullable|numeric',
            'ppn' => 'required|numeric',
            'status' => 'required|string|in:active,notactive',
        ]);

        $channel->update($request->all());

        return redirect()->route('admin.channel.CHPay')->with('success', 'Channel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $channel = ChannelPay::findOrFail($id);
        $channel->delete();
        return redirect()->back()->with('success', 'Channel berhasil dihapus.');
    }
}
