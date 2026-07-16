<?php

namespace App\Http\Controllers;

use App\Models\Lantai;
use App\Models\Reservasi;
use App\StatusReservasi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reservasiMendatang = Reservasi::with(['kelas'])
            ->where('id_user', $user->id)
            ->whereDate('tanggal', '>=', now())
            ->where('status', '!=', StatusReservasi::DITOLAK->value)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->first();

        $lantai = Lantai::withCount('kelas')->get();

        return view('pages.home', compact('user', 'reservasiMendatang', 'lantai'));
    }
}
