<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected ?string $apiKey;
    protected string $model;
    protected int $cacheMinutes;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-3.1-flash-lite');
        $this->cacheMinutes = (int) config('services.gemini.cache_minutes', 360);
    }

    /**
     * Susun rekomendasi ruangan berdasarkan konteks lengkap: popularitas,
     * histori personal, slot kosong, peringatan ruangan padat, dan filter kebutuhan.
     *
     * @param  array{
     *     global: \Illuminate\Support\Collection,
     *     personal: ?\Illuminate\Support\Collection,
     *     slot_kosong: array,
     *     peringatan: array,
     *     filter: ?array
     * } $context
     */
    public function recommendClassroom(array $context): array
    {
        $global = collect($context['global'] ?? [])->values();
        $personal = isset($context['personal']) ? collect($context['personal'])->values() : null;

        if ($global->isEmpty() && ($personal === null || $personal->isEmpty())) {
            return [
                'text' => 'Belum ada data riwayat reservasi yang cukup untuk memberikan rekomendasi ruangan.',
                'source' => 'fallback',
            ];
        }

        $cacheKey = 'gemini_room_recommendation_' . md5(json_encode($context));

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function () use ($context) {
            if (empty($this->apiKey)) {
                return [
                    'text' => $this->buildFallbackText($context),
                    'source' => 'fallback',
                ];
            }

            $prompt = $this->buildPrompt($context);
            $aiText = $this->callGemini($prompt);

            if ($aiText === null) {
                return [
                    'text' => $this->buildFallbackText($context),
                    'source' => 'fallback',
                ];
            }

            return [
                'text' => $aiText,
                'source' => 'ai',
            ];
        });
    }

    protected function buildPrompt(array $context): string
    {
        $global = collect($context['global'] ?? [])->values();
        $personal = isset($context['personal']) ? collect($context['personal'])->values() : null;
        $slotKosong = $context['slot_kosong'] ?? [];
        $peringatan = $context['peringatan'] ?? [];
        $filter = $context['filter'] ?? null;

        $bagian = [];

        if ($filter && (!empty($filter['kapasitas_min']) || !empty($filter['nama_lantai']))) {
            $keb = [];
            if (!empty($filter['kapasitas_min'])) {
                $keb[] = "kapasitas minimal {$filter['kapasitas_min']} orang";
            }
            if (!empty($filter['nama_lantai'])) {
                $keb[] = "di {$filter['nama_lantai']}";
            }
            $bagian[] = "KEBUTUHAN USER: " . implode(', ', $keb) . ". Prioritaskan ruangan yang sesuai kriteria ini.";
        }

        if ($personal && $personal->isNotEmpty()) {
            $daftarPersonal = $personal->map(fn ($item, $i) =>
                ($i + 1) . ". {$item['nama_kelas']} (Lantai {$item['nama_lantai']}, kapasitas {$item['kapasitas']}) — user ini sudah pakai {$item['total_reservasi']} kali"
            )->implode("\n");
            $bagian[] = "HISTORI PRIBADI USER (30 hari terakhir):\n{$daftarPersonal}";
        } else {
            $bagian[] = "HISTORI PRIBADI USER: belum ada riwayat reservasi, gunakan data tren umum saja.";
        }

        $daftarGlobal = $global->map(fn ($item, $i) =>
            ($i + 1) . ". {$item['nama_kelas']} (Lantai {$item['nama_lantai']}, kapasitas {$item['kapasitas']}) — {$item['total_reservasi']}x dipakai (semua user, 30 hari terakhir)"
        )->implode("\n");
        $bagian[] = "TREN UMUM (ruangan yang paling sering digunakan oleh seluruh pengguna):\n{$daftarGlobal}";

        if (!empty($slotKosong)) {
            $daftarSlot = collect($slotKosong)->map(fn ($slots, $nama) =>
                "- {$nama}: " . (empty($slots) ? "tidak ada slot kosong signifikan hari ini/besok" : implode(', ', $slots))
            )->implode("\n");
            $bagian[] = "SLOT JAM KOSONG (hari ini & besok, jam operasional 07:00-17:00):\n{$daftarSlot}";
        }

        if (!empty($peringatan)) {
            $daftarPeringatan = collect($peringatan)->map(function ($info, $nama) {
                $alt = $info['alternatif'] ?? null;
                $altText = $alt ? "Alternatif yang lebih longgar: {$alt}." : "Belum ditemukan alternatif setara.";
                return "- {$nama} okupansi {$info['occupancy']}% dalam 7 hari ke depan (PADAT). {$altText}";
            })->implode("\n");
            $bagian[] = "PERINGATAN RUANGAN PADAT:\n{$daftarPeringatan}";
        }

        $konteks = implode("\n\n", $bagian);

        return <<<PROMPT
        Anda adalah asisten AI pada sistem reservasi ruang kelas kampus.

        Semua data di bawah ini berasal langsung dari database.
        JANGAN mengarang nama ruangan, kapasitas, lantai, jam kosong, ataupun informasi lain yang tidak terdapat pada data.

        {$konteks}

        Tugas:

        1. Berikan rekomendasi maksimal 3 ruangan.

        2. Prioritas rekomendasi:
        - Jika tersedia histori reservasi pengguna
        - Jika tidak ada histori pribadi, gunakan data TREN UMUM.
        - Jika pengguna memiliki kebutuhan kapasitas atau lantai

        3. Jika tersedia SLOT JAM KOSONG, sebutkan slot kosong terbaik untuk setiap ruangan yang direkomendasikan.

        4. Jika salah satu ruangan memiliki PERINGATAN PADAT, beri peringatan singkat lalu arahkan ke alternatif yang tersedia.

        5. Jangan pernah menyebut ruangan yang tidak ada pada data.

        6. Jangan mengulang informasi yang sama.

        7. Gunakan Bahasa Indonesia yang formal, sopan, profesional, dan mudah dipahami. Gunakan kata   "Anda", hindari penggunaan kata "kamu", "nggak", "ya", "waduh", atau bahasa percakapan lainnya.

        8. Jawaban maksimal sekitar 120 kata.

        Contoh format jawaban:

        Berikut rekomendasi ruangan untuk Anda:

        1. Ruang A
        - Lantai ...
        - Kapasitas ...
        - Slot kosong: ...

        2. Ruang B
        - Lantai ...
        - Kapasitas ...
        - Slot kosong: ...

        Catatan:
        - Ruang C sedang memiliki tingkat okupansi tinggi, sehingga lebih disarankan menggunakan Ruang B sebagai alternatif.

        Jangan menambahkan salam pembuka ataupun penutup.
        PROMPT;
    }

    protected function callGemini(string $prompt): ?string
{
    try {
        Log::info('Gemini request', [
            'has_key' => !empty($this->apiKey),
            'key_prefix' => substr($this->apiKey ?? '', 0, 4),
            'model' => $this->model,
            'endpoint' => "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent",
        ]);

        $response = Http::timeout(30)
            ->withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent",
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.6,
                        'maxOutputTokens' => 600,
                    ],
                ]
            );

        Log::info('Gemini response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->successful()) {
            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if ($text) {
                return trim($text);
            }

            Log::warning('Gemini berhasil tetapi text kosong.', [
                'json' => $response->json(),
            ]);

            return null;
        }

        Log::error('Gemini API gagal.', [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body(),
        ]);

        return null;

    } catch (\Throwable $e) {

        Log::error('Gemini Exception', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return null;
    }
}

    /**
     * Parse pesan bebas dari user (mis. "carikan ruangan kosong besok jam 2-4 siang
     * kapasitas 40 di lantai 3") jadi filter terstruktur, memakai Gemini untuk NLU saja.
     * Pencarian ruangan & pengecekan ketersediaan TETAP dilakukan di database oleh
     * controller, bukan oleh AI — supaya hasilnya nggak pernah mengarang data.
     *
     * @param  string  $message  Pesan asli dari user.
     * @param  \Illuminate\Support\Collection  $daftarLantai  Koleksi Lantai (id, nama_lantai).
     * @return array{
     *     tanggal: ?string, jam_mulai: ?string, jam_selesai: ?string,
     *     kapasitas_min: ?int, id_lantai: ?int, catatan: ?string
     * }|null  Null kalau parsing gagal total (API key kosong / error).
     */
    public function parseBookingIntent(string $message, $daftarLantai): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $daftarLantaiText = collect($daftarLantai)
            ->map(fn ($l) => "id={$l->id} nama=\"{$l->nama_lantai}\"")
            ->implode(', ');

        $hariIni = now()->format('Y-m-d (l)');

        $prompt = <<<PROMPT
        Anda adalah parser Bahasa Indonesia untuk sistem reservasi ruang kelas kampus.

        Tugas Anda HANYA mengubah pesan pengguna menjadi JSON yang valid.
        Jangan menjawab pertanyaan.
        Jangan memberikan penjelasan.
        Jangan menggunakan markdown.
        Jangan menggunakan ```json.

        Format yang WAJIB:

        {
        "tanggal": "YYYY-MM-DD atau null",
        "jam_mulai": "HH:MM atau null",
        "jam_selesai": "HH:MM atau null",
        "kapasitas_min": angka atau null,
        "id_lantai": angka atau null,
        "catatan": "string atau null"
        }

        Hari ini:
        {$hariIni}

        Daftar lantai yang valid:
        {$daftarLantaiText}

        Aturan:

        1. Konversi kata:
        - hari ini
        - besok
        - lusa
        - Senin
        - Selasa
        - dst

        menjadi tanggal YYYY-MM-DD.

        2. Konversi jam alami:

        jam 2 pagi = 02:00
        jam 2 siang = 14:00
        jam 2 sore = 14:00
        jam 7 malam = 19:00
        jam 9 = 09:00

        3. Apabila pengguna menulis:

        jam 2-4
        2 sampai 4
        2 s.d.4
        14-16

        isi:

        jam_mulai
        jam_selesai

        4. Apabila pengguna hanya menyebutkan satu jam

        jam_mulai = jam tersebut
        jam_selesai = +2 jam

        contoh:

        jam 14

        menjadi

        14:00
        16:00

        5. Apabila pengguna tidak menyebutkan jam:

        jam_mulai = null
        jam_selesai = null

        6. Apabila pengguna tidak menyebutkan kapasitas:

        kapasitas_min = null

        7. Cocokkan lantai HANYA dari daftar berikut:

        {$daftarLantaiText}

        Jika tidak cocok,
        id_lantai = null.

        8. Jangan mengarang data.

        9. catatan hanya dipakai jika informasi user kurang jelas.

        10. Keluarkan JSON SAJA.

        Pesan pengguna:

        {$message}

        JSON:
        PROMPT;

        $text = $this->callGemini($prompt);

        if ($text === null) {
            return null;
        }

        // Bersihkan kalau Gemini tetap membungkus dengan markdown code fence.
        $clean = trim(preg_replace('/^```(json)?|```$/m', '', trim($text)));

        $parsed = json_decode($clean, true);

        if (!is_array($parsed)) {
            Log::warning('Gagal parse JSON dari Gemini (booking intent).', ['raw' => $text]);
            return null;
        }

        return [
            'tanggal' => $parsed['tanggal'] ?? null,
            'jam_mulai' => $parsed['jam_mulai'] ?? null,
            'jam_selesai' => $parsed['jam_selesai'] ?? null,
            'kapasitas_min' => isset($parsed['kapasitas_min']) ? (int) $parsed['kapasitas_min'] : null,
            'id_lantai' => isset($parsed['id_lantai']) ? (int) $parsed['id_lantai'] : null,
            'catatan' => $parsed['catatan'] ?? null,
        ];
    }

    /**
     * Rekomendasi cadangan tanpa AI, disusun manual dari semua konteks yang tersedia.
     */
    protected function buildFallbackText(array $context): string
    {
        $global = collect($context['global'] ?? [])->values();
        $personal = isset($context['personal']) ? collect($context['personal'])->values() : null;
        $slotKosong = $context['slot_kosong'] ?? [];
        $peringatan = $context['peringatan'] ?? [];

        $lines = [];

        if ($personal && $personal->isNotEmpty()) {
            $lines[] = "Berdasarkan riwayat reservasi Anda:";
            foreach ($personal->take(3) as $i => $item) {
                $lines[] = ($i + 1) . ". {$item['nama_kelas']} (Lantai {$item['nama_lantai']}, kapasitas {$item['kapasitas']}) — telah digunakan sebanyak {$item['total_reservasi']} kali.";
            }
        } else {
            $lines[] = "Berikut adalah ruangan yang paling sering digunakan berdasarkan tren reservasi:";
            foreach ($global->take(3) as $i => $item) {
                $lines[] = ($i + 1) . ". {$item['nama_kelas']} (Lantai {$item['nama_lantai']}, kapasitas {$item['kapasitas']}) — digunakan sebanyak {$item['total_reservasi']} kali.";
            }
        }

        if (!empty($slotKosong)) {
            $lines[] = "";
            $lines[] = "Slot waktu yang masih tersedia:";
            foreach ($slotKosong as $nama => $slots) {
                $lines[] = "- {$nama}: " . (empty($slots)
                    ? "Belum tersedia slot kosong yang signifikan."
                    : implode(', ', $slots));
            }
        }

        if (!empty($peringatan)) {
            $lines[] = "";
            $lines[] = "Peringatan tingkat okupansi:";
            foreach ($peringatan as $nama => $info) {
                $alt = $info['alternatif'] ?? 'Belum tersedia alternatif yang setara.';
                $lines[] = "- {$nama} memiliki tingkat okupansi {$info['occupancy']}%. Disarankan mempertimbangkan alternatif: {$alt}.";
            }
        }

        return implode("\n", $lines);
    }
}