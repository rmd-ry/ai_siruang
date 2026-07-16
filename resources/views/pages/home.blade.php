@extends('layouts.app')

@section('content')
<div class="py-6 min-h-screen">
    {{-- Header / Top Section --}}
    <div class="flex flex-col lg:flex-row items-stretch justify-between mx-4 sm:mx-6 lg:mx-12 gap-6">

        {{-- Side left (Greeting & Action) - Disatukan dalam 1 Blok Solid --}}
        <div class="flex-1 bg-gradient-to-br from-blue-50/60 to-transparent p-5 sm:p-6 rounded-2xl border border-blue-100/40 flex flex-col justify-between">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-Title tracking-tight leading-snug">
                    Selamat datang, {{ explode(' ', $user->nama)[0] }} 👋
                </h1>
                <p class="text-body text-xs sm:text-sm md:text-base mt-2 max-w-xl">
                    Jadwalkan kegiatan perkuliahan dan pertemuanmu dengan mudah hari ini.
                </p>
            </div>

            <div class="mt-4 sm:mt-6">
                <a href="{{ route('ruangan.index') }}"
                    class="inline-flex items-center justify-center w-full sm:w-auto text-center bg-secondary hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition duration-200 cursor-pointer text-sm">
                    <i class="bi bi-plus-circle mr-2"></i> Reservasi Sekarang
                </a>
            </div>
        </div>

        {{-- Side right (Widget Reservasi Terdekat) --}}
        <div class="w-full lg:w-1/3 xl:w-96 flex">
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-Subtle flex flex-col justify-between w-full">
                <div>
                    <h3 class="text-base sm:text-lg font-bold flex items-center mb-4 text-Title">
                        <i class="bi bi-calendar3 mr-3 text-secondary text-lg"></i>
                        Reservasi Berikutnya
                    </h3>

                    @if ($reservasiMendatang)
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-inverse font-bold text-base sm:text-lg truncate">{{ $reservasiMendatang->kelas->nama_kelas }}</p>
                                <p class="text-body text-xs sm:text-sm mt-0.5">
                                    {{ \Carbon\Carbon::parse($reservasiMendatang->tanggal)->translatedFormat('l, d M Y') }}
                                </p>
                                <p class="text-body text-xs sm:text-sm font-mono mt-1 bg-gray-50 px-2 py-0.5 rounded inline-block">
                                    {{ \Carbon\Carbon::parse($reservasiMendatang->jam_mulai)->format('H:i') }} WIB
                                </p>
                            </div>

                            <span class="px-2.5 py-1 text-[10px] sm:text-xs font-semibold rounded-full flex-shrink-0 whitespace-nowrap
                                {{ $reservasiMendatang->status === \App\StatusReservasi::DITERIMA ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                @if ($reservasiMendatang->status === \App\StatusReservasi::DITERIMA)
                                    Disetujui <i class="bi bi-check-circle-fill ml-1"></i>
                                @else
                                    Pending <i class="bi bi-hourglass-split ml-1"></i>
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-400">
                            <i class="bi bi-calendar-x text-3xl mb-2 block text-gray-300"></i>
                            <p class="text-xs sm:text-sm">Belum ada jadwal reservasi mendatang.</p>
                        </div>
                    @endif
                </div>

                <a href="{{ route('riwayat') }}"
                    class="block mt-5 text-right text-secondary text-xs sm:text-sm hover:underline font-semibold transition duration-200">
                    Lihat semua riwayat &rarr;
                </a>
            </div>
        </div>

    </div>

    {{-- List Lantai / Lokasi --}}
    <div class="mt-12 mx-4 sm:mx-6 lg:mx-12">
        <h2 class="text-lg sm:text-xl font-bold mb-6 text-Title border-l-4 border-secondary pl-3">
            Pilih Lokasi Ruangan
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
            @forelse($lantai as $l)
                {{-- Link ke Filter Lantai --}}
                <a href="{{ route('ruangan.index', ['lantai' => $l->id]) }}" class="block group">
                    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-Subtle hover:shadow-md hover:border-secondary transition duration-200 cursor-pointer h-full flex flex-col items-center justify-center text-center">
                        <div class="mb-3 p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 text-secondary transition flex items-center justify-center">
                            <i class="bi bi-building-fill text-2xl"></i>
                        </div>
                        <span class="font-bold text-base sm:text-lg text-Title group-hover:text-secondary transition truncate w-full">
                            {{ $l->nama_lantai }}
                        </span>
                        <p class="text-body text-xs sm:text-sm mt-1 bg-gray-50 group-hover:bg-blue-50/50 px-2.5 py-0.5 rounded-full transition">
                            {{ $l->kelas_count }} ruang tersedia
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-300 px-4">
                    <i class="bi bi-inbox text-3xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-500 font-medium">Belum ada data lantai yang terdaftar. Hubungi Admin.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection