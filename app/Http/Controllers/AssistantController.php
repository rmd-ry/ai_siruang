<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Lantai;
use App\Models\Reservasi;
use App\Services\GeminiService;
use App\StatusReservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AssistantController extends Controller
{
    protected string $jamBuka = '08:00';
    protected string $jamTutup = '22:00';

    /**
     * Terima pesan bebas dari user, parse jadi filter terstruktur lewat Gemini,
     * lalu cari ruangan yang benar-benar tersedia di database (bukan AI yang nebak).
     */
    public function tanya(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:300',
        ]);

        $pesan = trim($request->input('message'));
        $daftarLantai = Lantai::orderBy('id')->get();

        $intent = app(GeminiService::class)
            ->parseBookingIntent($pesan, $daftarLantai);

        if ($intent === null) {
            return response()->json([
                'reply' => 'Maaf, layanan asisten AI sedang tidak tersedia. Silakan menggunakan fitur pencarian ruangan pada halaman Ruangan.',
                'rooms' => [],
                'fallback' => true,
            ]);
        }

        // Default tanggal = hari ini jika user tidak menyebut tanggal atau hasil parsing tidak valid.
        $tanggal = $intent['tanggal'] ?? null;

        if (empty($tanggal) || !$this->tanggalValid($tanggal)) {
            $tanggal = now()->toDateString();
        }

        $jamMulai = $this->jamValid($intent['jam_mulai'] ?? null) ? $intent['jam_mulai'] : null;
        $jamSelesai = $this->jamValid($intent['jam_selesai'] ?? null) ? $intent['jam_selesai'] : null;

        // Kalau cuma salah satu jam yang valid, anggap dua-duanya nggak lengkap
        // (biar tidak salah asumsi durasi), balik ke mode "cari yang kosong hari itu".
        if (($jamMulai && !$jamSelesai) || (!$jamMulai && $jamSelesai)) {
            $jamMulai = null;
            $jamSelesai = null;
        }

        $query = Kelas::with('lantai');

        if ($intent['id_lantai']) {
            $query->where('id_lantai', $intent['id_lantai']);
        }
        if ($intent['kapasitas_min']) {
            $query->where('kapasitas', '>=', $intent['kapasitas_min']);
        }

        $kandidatRuangan = $query->orderBy('kapasitas')->get();

        if ($kandidatRuangan->isEmpty()) {
            return response()->json([
                'reply' => 'Tidak ditemukan ruangan yang sesuai dengan kriteria yang Anda masukkan. Silakan ubah atau kurangi kriteria pencarian, kemudian coba kembali.',
                'rooms' => [],
            ]);
        }

        if ($jamMulai && $jamSelesai) {
            $hasil = $this->cariRuanganTersediaJamTertentu($kandidatRuangan, $tanggal, $jamMulai, $jamSelesai);
            $reply = $this->susunReplyDenganJam($hasil, $tanggal, $jamMulai, $jamSelesai, $intent['catatan']);
        } else {
            $hasil = $this->cariRuanganDenganSlotKosong($kandidatRuangan, $tanggal);
            $reply = $this->susunReplyTanpaJam($hasil, $tanggal, $intent['catatan']);
        }

        return response()->json([
            'reply' => $reply,
            'rooms' => $hasil,
        ]);
    }

    protected function tanggalValid(?string $tanggal): bool
    {
        if (!$tanggal) {
            return false;
        }

        try {
            $d = Carbon::parse($tanggal);
            return $d->greaterThanOrEqualTo(today());
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function jamValid(?string $jam): bool
    {
        if (!$jam) {
            return false;
        }

        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $jam);
    }

    /**
     * Cari ruangan dari kandidat yang benar-benar kosong di jam yang diminta persis.
     */
    protected function cariRuanganTersediaJamTertentu($kandidatRuangan, string $tanggal, string $jamMulai, string $jamSelesai): array
    {
        $hasil = [];

        foreach ($kandidatRuangan as $ruang) {
            $bentrok = Reservasi::where('id_kelas', $ruang->id)
                ->where('tanggal', $tanggal)
                ->where('status', '!=', StatusReservasi::DITOLAK->value)
                ->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai)
                ->exists();

            if (!$bentrok) {
                $hasil[] = [
                    'id_kelas' => $ruang->id,
                    'nama_kelas' => $ruang->nama_kelas,
                    'nama_lantai' => optional($ruang->lantai)->nama_lantai ?? '-',
                    'kapasitas' => $ruang->kapasitas,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'tanggal' => $tanggal,
                    'link_booking' => route('reservasi.form', [
                        'class_id' => $ruang->id,
                        'date' => $tanggal,
                        'start' => $jamMulai,
                        'end' => $jamSelesai,
                    ]),
                ];
            }

            if (count($hasil) >= 5) {
                break;
            }
        }

        return $hasil;
    }

    /**
     * Kalau user nggak sebut jam spesifik: carikan ruangan + slot kosong terbaik hari itu.
     */
    protected function cariRuanganDenganSlotKosong($kandidatRuangan, string $tanggal): array
    {
        $hasil = [];

        foreach ($kandidatRuangan as $ruang) {
            $booked = Reservasi::where('id_kelas', $ruang->id)
                ->where('tanggal', $tanggal)
                ->where('status', '!=', StatusReservasi::DITOLAK->value)
                ->orderBy('jam_mulai')
                ->get(['jam_mulai', 'jam_selesai']);

            $gaps = $this->cariCelahWaktu($booked);

            if (!empty($gaps)) {
                $hasil[] = [
                    'id_kelas' => $ruang->id,
                    'nama_kelas' => $ruang->nama_kelas,
                    'nama_lantai' => optional($ruang->lantai)->nama_lantai ?? '-',
                    'kapasitas' => $ruang->kapasitas,
                    'slot_kosong' => array_map(fn ($g) => "{$g['mulai']}-{$g['selesai']}", $gaps),
                    'tanggal' => $tanggal,
                    'link_booking' => route('reservasi.create', [
                        'class_id' => $ruang->id,
                        'date' => $tanggal,
                    ]),
                ];
            }

            if (count($hasil) >= 5) {
                break;
            }
        }

        return $hasil;
    }

    protected function cariCelahWaktu($booked): array
    {
        $mulaiOperasional = Carbon::parse($this->jamBuka);
        $tutupOperasional = Carbon::parse($this->jamTutup);

        $penanda = $mulaiOperasional->copy();
        $gaps = [];

        foreach ($booked as $item) {
            $mulaiBooking = Carbon::parse($item->jam_mulai);
            $selesaiBooking = Carbon::parse($item->jam_selesai);

            if ($mulaiBooking->gt($penanda)) {
                if ($penanda->diffInMinutes($mulaiBooking) >= 60) {
                    $gaps[] = ['mulai' => $penanda->format('H:i'), 'selesai' => $mulaiBooking->format('H:i')];
                }
            }

            if ($selesaiBooking->gt($penanda)) {
                $penanda = $selesaiBooking->copy();
            }
        }

        if ($penanda->lt($tutupOperasional) && $penanda->diffInMinutes($tutupOperasional) >= 60) {
            $gaps[] = ['mulai' => $penanda->format('H:i'), 'selesai' => $tutupOperasional->format('H:i')];
        }

        return $gaps;
    }

    protected function susunReplyDenganJam(array $hasil, string $tanggal, string $jamMulai, string $jamSelesai, ?string $catatan): string
    {
        $tanggalText = Carbon::parse($tanggal)
            ->locale('id')
            ->translatedFormat('l, d F Y');

        if (empty($hasil)) {
            return "Tidak ditemukan ruangan yang tersedia pada {$tanggalText} pukul {$jamMulai}-{$jamSelesai}. Silakan memilih waktu atau ruangan lain.";
        }

        $reply = "Berikut daftar ruangan yang tersedia pada {$tanggalText} pukul {$jamMulai}-{$jamSelesai}:";

        if ($catatan) {
            $reply .= " ({$catatan})";
        }

        return $reply;
    }

    protected function susunReplyTanpaJam(array $hasil, string $tanggal, ?string $catatan): string
    {
        $tanggalText = Carbon::parse($tanggal)
            ->locale('id')
            ->translatedFormat('l, d F Y');

        if (empty($hasil)) {
            return "Tidak ditemukan ruangan yang memiliki slot waktu tersedia pada {$tanggalText}. Silakan memilih tanggal lain.";
        }

        $reply = "Karena Anda tidak menentukan waktu secara spesifik, berikut daftar ruangan beserta slot waktu yang masih tersedia pada {$tanggalText}:";

        if ($catatan) {
            $reply .= " ({$catatan})";
        }

        return $reply;
    }
}
