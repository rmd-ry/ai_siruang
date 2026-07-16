<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Lantai;
use App\Models\Reservasi;
use App\Services\GeminiService;
use App\StatusReservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RuangController extends Controller
{
    // Jam operasional kampus, dipakai untuk hitung slot kosong & okupansi.
    // Harus sinkron dengan rentang jam di ReservasiController@create (08:00-22:00).
    protected string $jamBuka = '08:00';
    protected string $jamTutup = '22:00';

    public function index(Request $request)
    {
        $semuaLantai = Lantai::orderBy('id')->get();

        $query = Kelas::with('lantai');

        if ($request->filled('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        if ($request->has('lantai')) {
            $query->where('id_lantai', $request->lantai);
            $namaLantaiTerpilih = optional($semuaLantai->firstWhere('id', $request->lantai))->nama_lantai ?? 'Pilih Lantai';
        } else {
            $namaLantaiTerpilih = 'Pilih Lantai';
        }

        $semuaKelas = $query->paginate(12)->withQueryString();

        // ==== Filter kebutuhan khusus untuk kotak rekomendasi AI ====
        // Lantai sengaja TIDAK punya kontrol terpisah di sini — AI otomatis
        // mengikuti filter lantai utama (dropdown di luar), supaya user cuma
        // perlu satu tempat untuk pilih lantai dan hasilnya konsisten di semua bagian.
        $kapasitasMin = $request->filled('kebutuhan_kapasitas') ? (int) $request->kebutuhan_kapasitas : null;
        $lantaiRekomendasi = $request->has('lantai') ? (int) $request->lantai : null;
        $namaLantaiRekomendasi = $lantaiRekomendasi ? $namaLantaiTerpilih : null;

        // ==== 1 & 4: Rekomendasi global (tren umum) + personal, dengan filter kebutuhan ====
        $globalStats = $this->statistikPemakaian(null, $kapasitasMin, $lantaiRekomendasi);
        $personalStats = Auth::check()
            ? $this->statistikPemakaian(Auth::id(), $kapasitasMin, $lantaiRekomendasi)
            : null;

        // Ambil kandidat ruangan yang mau dianalisis lebih lanjut (slot kosong & okupansi):
        // gabungan top ruangan personal (kalau ada) + top ruangan global, tanpa duplikat.
        $kandidat = collect($personalStats)->merge($globalStats)
            ->unique('nama_kelas')
            ->take(4)
            ->values();

        // ==== 2: Slot kosong untuk kandidat ruangan ====
        $slotKosong = $this->slotKosongUntukKandidat($kandidat);

        // ==== 3: Peringatan ruangan padat + cari alternatif ====
        $peringatan = $this->peringatanRuanganPadat($kandidat, $kapasitasMin, $lantaiRekomendasi);

        $geminiService = new GeminiService();
        $rekomendasi = $geminiService->recommendClassroom([
            'global' => $globalStats,
            'personal' => $personalStats,
            'slot_kosong' => $slotKosong,
            'peringatan' => $peringatan,
            'filter' => [
                'kapasitas_min' => $kapasitasMin,
                'nama_lantai' => $namaLantaiRekomendasi,
            ],
        ]);

        return view('pages.ruangan', compact(
            'semuaLantai',
            'semuaKelas',
            'namaLantaiTerpilih',
            'rekomendasi',
            'kapasitasMin',
            'lantaiRekomendasi',
            'namaLantaiRekomendasi',
            'slotKosong',
            'peringatan'
        ));
    }

    /**
     * Statistik pemakaian ruangan (30 hari terakhir). Kalau $userId diisi,
     * hasilnya khusus histori user tsb (dipakai untuk rekomendasi personal).
     */
    protected function statistikPemakaian(?int $userId, ?int $kapasitasMin, ?int $lantaiId, int $limit = 5)
    {
        $query = Reservasi::selectRaw('id_kelas, COUNT(*) as total_reservasi')
            ->where('status', '!=', StatusReservasi::DITOLAK->value)
            ->where('tanggal', '>=', now()->subDays(30))
            ->groupBy('id_kelas');

        if ($userId) {
            $query->where('id_user', $userId);
        }

        if ($kapasitasMin || $lantaiId) {
            $query->whereHas('kelas', function ($q) use ($kapasitasMin, $lantaiId) {
                if ($kapasitasMin) {
                    $q->where('kapasitas', '>=', $kapasitasMin);
                }
                if ($lantaiId) {
                    $q->where('id_lantai', $lantaiId);
                }
            });
        }

        return $query->orderByDesc('total_reservasi')
            ->limit($limit)
            ->with('kelas.lantai')
            ->get()
            ->filter(fn ($item) => $item->kelas !== null)
            ->map(fn ($item) => [
                'id_kelas' => $item->kelas->id,
                'nama_kelas' => $item->kelas->nama_kelas,
                'nama_lantai' => optional($item->kelas->lantai)->nama_lantai ?? '-',
                'kapasitas' => $item->kelas->kapasitas,
                'total_reservasi' => $item->total_reservasi,
            ])
            ->values();
    }

    /**
     * Cari slot jam kosong hari ini & besok untuk tiap ruangan kandidat,
     * berdasarkan jam operasional dikurangi jam yang sudah dibooking.
     */
    protected function slotKosongUntukKandidat(\Illuminate\Support\Collection $kandidat): array
    {
        $hasil = [];

        foreach ($kandidat as $ruang) {
            $slotRuang = [];

            foreach (['Hari ini' => today(), 'Besok' => today()->addDay()] as $label => $tanggal) {
                $booked = Reservasi::where('id_kelas', $ruang['id_kelas'])
                    ->where('tanggal', $tanggal->toDateString())
                    ->where('status', '!=', StatusReservasi::DITOLAK->value)
                    ->orderBy('jam_mulai')
                    ->get(['jam_mulai', 'jam_selesai']);

                $gaps = $this->cariCelahWaktu($booked);

                foreach ($gaps as $gap) {
                    $slotRuang[] = "{$label} {$gap['mulai']}-{$gap['selesai']}";
                }
            }

            $hasil[$ruang['nama_kelas']] = array_slice($slotRuang, 0, 3);
        }

        return $hasil;
    }

    /**
     * Hitung celah waktu kosong (minimal 1 jam) di antara jam operasional,
     * berdasarkan daftar reservasi yang sudah ada pada satu tanggal.
     */
    protected function cariCelahWaktu(\Illuminate\Support\Collection $booked): array
    {
        $mulaiOperasional = Carbon::parse($this->jamBuka);
        $tutupOperasional = Carbon::parse($this->jamTutup);

        $penanda = $mulaiOperasional->copy();
        $gaps = [];

        foreach ($booked as $item) {
            $mulaiBooking = Carbon::parse($item->jam_mulai);
            $selesaiBooking = Carbon::parse($item->jam_selesai);

            if ($mulaiBooking->gt($penanda)) {
                $durasiMenit = $penanda->diffInMinutes($mulaiBooking);
                if ($durasiMenit >= 60) {
                    $gaps[] = ['mulai' => $penanda->format('H:i'), 'selesai' => $mulaiBooking->format('H:i')];
                }
            }

            if ($selesaiBooking->gt($penanda)) {
                $penanda = $selesaiBooking->copy();
            }
        }

        if ($penanda->lt($tutupOperasional)) {
            $durasiMenit = $penanda->diffInMinutes($tutupOperasional);
            if ($durasiMenit >= 60) {
                $gaps[] = ['mulai' => $penanda->format('H:i'), 'selesai' => $tutupOperasional->format('H:i')];
            }
        }

        return $gaps;
    }

    /**
     * Deteksi ruangan yang okupansinya tinggi (7 hari ke depan) di antara kandidat,
     * lalu carikan alternatif ruangan sejenis (lantai/kapasitas mirip) yang lebih longgar.
     */
    protected function peringatanRuanganPadat(\Illuminate\Support\Collection $kandidat, ?int $kapasitasMin, ?int $lantaiId, float $ambangBatas = 70.0): array
    {
        $peringatan = [];
        $totalJamTersedia = Carbon::parse($this->jamBuka)->diffInHours(Carbon::parse($this->jamTutup)) * 7; // 7 hari ke depan

        foreach ($kandidat as $ruang) {
            $jamTerpakai = Reservasi::where('id_kelas', $ruang['id_kelas'])
                ->whereBetween('tanggal', [today(), today()->addDays(6)])
                ->where('status', '!=', StatusReservasi::DITOLAK->value)
                ->get()
                ->sum(fn ($r) => Carbon::parse($r->jam_mulai)->diffInHours(Carbon::parse($r->jam_selesai)));

            $okupansi = $totalJamTersedia > 0 ? round(($jamTerpakai / $totalJamTersedia) * 100) : 0;

            if ($okupansi >= $ambangBatas) {
                $alternatif = Kelas::with('lantai')
                    ->where('id', '!=', $ruang['id_kelas'])
                    ->when($kapasitasMin, fn ($q) => $q->where('kapasitas', '>=', $kapasitasMin))
                    ->when($lantaiId, fn ($q) => $q->where('id_lantai', $lantaiId))
                    ->when(!$kapasitasMin, fn ($q) => $q->whereBetween('kapasitas', [$ruang['kapasitas'] * 0.7, $ruang['kapasitas'] * 1.3]))
                    ->whereDoesntHave('reservasi', function ($q) use ($totalJamTersedia) {
                        // ruangan alternatif: cari yang okupansinya jelas lebih rendah (heuristik sederhana)
                        $q->whereBetween('tanggal', [today(), today()->addDays(6)])
                            ->where('status', '!=', StatusReservasi::DITOLAK->value);
                    })
                    ->first();

                $peringatan[$ruang['nama_kelas']] = [
                    'occupancy' => $okupansi,
                    'alternatif' => $alternatif ? "{$alternatif->nama_kelas} (Lantai {$alternatif->lantai->nama_lantai})" : null,
                ];
            }
        }

        return $peringatan;
    }
}