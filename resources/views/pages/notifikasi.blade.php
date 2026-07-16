@extends('layouts.app')

@section('content')
    <div class="py-4 max-w-6xl mx-auto px-2 sm:px-4">

        {{-- Header Halaman --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-5">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="bi bi-inbox text-blue-600"></i> Kotak Masuk
                </h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-0.5">Pembaruan resmi terkait status pengajuan reservasi ruangan Anda.</p>
            </div>
            
            {{-- Tombol Bersihkan jika ada data --}}
            @if($notifikasi->isNotEmpty())
                <form action="{{ route('notifikasi.clear') }}" method="POST" class="self-start sm:self-center">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl text-xs font-semibold border border-gray-200 shadow-sm transition cursor-pointer">
                        <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Grid Wrapper --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            @forelse($notifikasi as $item)
                @php
                    $isApproved = $item->status === \App\StatusReservasi::DITERIMA;
                @endphp

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition relative overflow-hidden group">
                    
                    {{-- Dekorasi Garis Status --}}
                    <div class="absolute top-0 left-0 right-0 h-[4px] {{ $isApproved ? 'bg-green-500' : 'bg-red-500' }}"></div>

                    <div>
                        {{-- Header Card: Status --}}
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $isApproved ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                <i class="bi {{ $isApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                {{ $isApproved ? 'Disetujui' : 'Ditolak' }}
                            </span>
                        </div>

                        {{-- Info Ruangan --}}
                        <div class="mb-4">
                            <p class="text-[10px] text-gray-400 uppercase font-extrabold tracking-wider">Ruangan Pengajuan</p>
                            <h3 class="text-base font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $item->kelas->nama_kelas }}</h3>
                        </div>

                        {{-- Detail Waktu --}}
                        <div class="space-y-2 border-t border-gray-50 pt-3">
                            <div class="flex items-center gap-2.5 text-xs text-gray-600">
                                <i class="bi bi-calendar3 text-gray-400"></i>
                                <span>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}</span>
                            </div>

                            <div class="flex items-center gap-2.5 text-xs text-gray-600">
                                <i class="bi bi-clock text-gray-400"></i>
                                <span class="font-mono bg-gray-50 px-2 py-0.5 rounded text-gray-700">{{ substr($item->jam_mulai, 0, 5) }} – {{ substr($item->jam_selesai, 0, 5) }} WIB</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Card: Waktu Update --}}
                    <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                        <span><i class="bi bi-shield-check opacity-70"></i> Sistem</span>
                        <span>{{ $item->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

            @empty
                {{-- State Empty --}}
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <i class="bi bi-bell-slash text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Kotak Masuk Kosong</h3>
                    <p class="text-xs text-gray-400 max-w-xs mt-1">Belum ada pembaruan status masuk dari admin untuk reservasi yang Anda ajukan.</p>
                </div>
            @endforelse

        </div>
    </div>
@endsection