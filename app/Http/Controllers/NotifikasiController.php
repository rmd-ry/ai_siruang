<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Reservasi;
use App\StatusReservasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{

    protected function markQueryAsRead($query)
    {
        return $query->update([
            'read_at' => now(),
        ]);
    }

    public function index()
    {

        $baseQuery = Reservasi::where('id_user', auth()->id())
            ->where('status', '!=', StatusReservasi::PENDING);

        $this->markQueryAsRead(
            (clone $baseQuery)->whereNull('read_at')
        );

        $notifikasi = $baseQuery
            ->with('kelas')
            ->latest('updated_at')
            ->get();

        return view('pages.notifikasi', compact('notifikasi'));
    }

    public function markAsRead($id)
    {
        $this->markQueryAsRead(
            Reservasi::where('id_user', auth()->id())
                ->where('id', $id)
        );

        return back();
    }
    public function clearAll()
    {
        $this->markQueryAsRead(
            Reservasi::where('id_user', auth()->id())
        );

        return back()->with('success', 'Semua notifikasi ditandai dibaca');
    }
}
