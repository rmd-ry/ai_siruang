@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row gap-6 md:gap-8 p-3 sm:p-6 mx-2 sm:mx-4 md:mx-8">

        {{-- Sidebar Filter --}}
        <div class="w-full md:w-64 flex flex-col gap-3">
            <h3 class="font-bold text-gray-700 mb-2 flex items-center">
                <i class="bi bi-funnel-fill mr-2 text-secondary"></i> Filter Status
            </h3>

            {{-- Helper function buat ngecek status aktif --}}
            @php
                $currentStatus = request('status');
                $btnBase = 'block text-center py-2.5 rounded-lg shadow-sm font-medium transition duration-200 border';
                $btnActive = 'bg-secondary text-white border-secondary';
                $btnInactive = 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-secondary';
            @endphp

            <a href="{{ route('riwayat') }}" class="{{ $btnBase }} {{ !$currentStatus ? $btnActive : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-600' }}">
                Semua
            </a>

            <a href="{{ route('riwayat', ['status' => 'disetujui']) }}"
                class="{{ $btnBase }} {{ $currentStatus == 'disetujui' ? 'bg-green-500 text-white': 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-600'}}">
                Disetujui
            </a>

            <a href="{{ route('riwayat', ['status' => 'menunggu']) }}"
                class="{{ $btnBase }} {{ $currentStatus == 'menunggu' ? 'bg-yellow-500 text-white'  : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-600' }}">
                Menunggu
            </a>

            <a href="{{ route('riwayat', ['status' => 'ditolak']) }}"
                class="{{ $btnBase }} {{ $currentStatus == 'ditolak' ? 'bg-red-500 text-white' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-600'}}">
                Ditolak
            </a>
        </div>

        {{-- List Riwayat (Looping) --}}
        <div class="flex-1 grid gap-6 grid-cols-1 xl:grid-cols-2 auto-rows-max">

            @forelse($riwayat as $item)
                @php
                    // Tentukan warna & icon berdasarkan status
                    $statusColor = 'text-gray-500';
                    $icon = 'bi-question-circle';
                    $statusLabel = 'Unknown';

                    if ($item->status === \App\StatusReservasi::DITERIMA) {
                        $statusColor = 'text-green-600';
                        $icon = 'bi-check-circle-fill';
                        $statusLabel = 'Disetujui';
                    } elseif ($item->status === \App\StatusReservasi::PENDING) {
                        $statusColor = 'text-yellow-500';
                        $icon = 'bi-hourglass-split';
                        $statusLabel = 'Menunggu';
                    } elseif ($item->status === \App\StatusReservasi::DITOLAK) {
                        $statusColor = 'text-red-600';
                        $icon = 'bi-x-circle-fill';
                        $statusLabel = 'Ditolak';
                    }
                @endphp

                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 flex justify-between items-start gap-3 sm:gap-4 w-full hover:shadow-md transition duration-200">
                    <div class="min-w-0">
                        <div class="flex items-center flex-wrap gap-2 mb-1">
                            <h2 class="text-base sm:text-lg font-bold text-gray-800 break-words">{{ $item->kelas->nama_kelas }}</h2>
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full border whitespace-nowrap {{ $statusColor }} bg-opacity-10 border-opacity-20">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Tanggal & Jam --}}
                        <div class="flex items-center flex-wrap gap-2 mt-2 text-gray-600 text-xs sm:text-sm">
                            <i class="bi bi-calendar3 text-secondary"></i>
                            <span>
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}
                                <span class="font-mono text-xs bg-gray-100 px-1 rounded ml-1">
                                    {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                                </span>
                            </span>
                        </div>

                        {{-- Waktu Update --}}
                        <div class="flex items-center gap-2 mt-1 text-gray-500 text-xs">
                            <i class="bi bi-clock-history"></i>
                            <span>Update: {{ $item->updated_at->diffForHumans() }}</span>
                        </div>

                        {{-- Alasan (Kegiatan) --}}
                        <div class="mt-3 text-xs sm:text-sm text-gray-700 italic border-l-2 border-gray-200 pl-3 break-words">
                            "{{ Str::limit($item->alasan, 50) }}"
                        </div>
                    </div>

                    {{-- Icon Status Besar di Kanan --}}
                    <i class="bi {{ $icon }} {{ $statusColor }} text-2xl sm:text-3xl opacity-80 flex-shrink-0"></i>
                </div>

            @empty
                {{-- Empty State --}}
                <div
                    class="col-span-full flex flex-col items-center justify-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <i class="bi bi-inbox text-4xl text-gray-300 mb-3"></i>
                    <h3 class="text-gray-900 font-medium">Tidak ada riwayat reservasi</h3>
                    <p class="text-gray-500 text-sm">Coba ubah filter atau buat reservasi baru.</p>
                </div>
            @endforelse

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mx-4 md:mx-8 mb-8">
        {{ $riwayat->links() }}
    </div>
@endsection