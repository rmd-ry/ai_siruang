@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-md text-center border border-gray-100">
        
        @if(session('status') == 'sukses')
            {{-- Icon & Tampilan Sukses --}}
            <div class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="bi bi-check-circle-fill text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Reservasi Berhasil!</h1>
            <p class="text-gray-600 text-sm mb-6">Pengajuan reservasi ruangan Anda telah berhasil dicatat. Silakan cek status persetujuan secara berkala.</p>
        @else
            {{-- Icon & Tampilan Gagal --}}
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="bi bi-x-circle-fill text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Reservasi Gagal</h1>
            <p class="text-gray-600 text-sm mb-6">{{ session('message') ?? 'Mohon maaf, terjadi kendala pada sistem atau slot waktu ruangan sudah terisi.' }}</p>
        @endif

        {{-- Aksi --}}
        <div class="flex flex-col gap-2">
            <a href="/riwayat" class="block w-full text-center bg-[#063970] hover:bg-opacity-90 text-white font-bold px-4 py-3 rounded-xl shadow transition text-sm">
                Lihat Riwayat Reservasi
            </a>
            <a href="{{ route('ruangan.index') }}" class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold px-4 py-3 rounded-xl transition text-sm">
                Kembali ke Ruangan
            </a>
        </div>
    </div>
</div>
@endsection