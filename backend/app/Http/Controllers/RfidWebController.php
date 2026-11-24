<?php

namespace App\Http\Controllers;

use App\Models\RfidScan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RfidWebController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentScans = RfidScan::with('user')->latest()->take(30)->get();
        $allUsersWithRfid = User::whereNotNull('rfid_uid')->get();

        return view('rfid.index', compact('user', 'recentScans', 'allUsersWithRfid'));
    }
}
