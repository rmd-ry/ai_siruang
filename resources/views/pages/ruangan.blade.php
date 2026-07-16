@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 min-h-screen">
    {{-- Header & Filter Utama --}}
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mx-2 sm:mx-4 md:mx-8 mb-8 gap-4">

        {{-- Side Left: Dropdown Lantai --}}
        <div id="lantaiWrapper" class="relative inline-block z-20 w-full sm:w-auto">
            <button id="btnLantai"
                class="flex items-center justify-between sm:justify-start gap-3 bg-secondary cursor-pointer text-white w-full sm:w-auto pl-6 pr-5 py-3 rounded-xl shadow-lg hover:bg-blue-700 transition duration-200">
                <span class="font-semibold tracking-wide text-sm sm:text-base">
                    {{ request('lantai') ? $namaLantaiTerpilih : 'Semua Lantai' }}
                </span>
                <i class="bi bi-chevron-down text-xs opacity-80"></i>
            </button>

            {{-- Dropdown Menu Responsif --}}
            <div id="dropdownLantai"
                class="absolute top-full sm:top-1/2 left-0 sm:left-full mt-2 sm:mt-0 sm:ml-4 -translate-y-0 sm:-translate-y-1/2 flex flex-col sm:flex-row gap-2 z-50 opacity-0 hidden transition-all duration-300 -translate-x-0 sm:-translate-x-5 w-full sm:w-auto bg-white sm:bg-transparent p-3 sm:p-0 rounded-xl shadow-xl sm:shadow-none border sm:border-0 border-gray-100">

                {{-- Tombol Reset (Semua) --}}
                <a href="{{ route('ruangan.index') }}"
                    class="bg-white text-gray-700 text-center hover:bg-blue-50 hover:text-secondary border border-gray-200 px-4 py-2 cursor-pointer rounded-lg shadow-md transition whitespace-nowrap font-medium text-sm">
                    Semua
                </a>

                {{-- Loop Data Lantai --}}
                @foreach ($semuaLantai as $l)
                    <a href="{{ route('ruangan.index', ['lantai' => $l->id]) }}"
                        class="bg-white text-gray-700 text-center hover:bg-blue-50 hover:text-secondary border border-gray-200 px-4 py-2 cursor-pointer rounded-lg shadow-md transition whitespace-nowrap font-medium text-sm">
                        {{ $l->nama_lantai }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Side Right: Search Bar --}}
        <div class="relative w-full sm:w-72 md:w-96">
            <form action="{{ route('ruangan.index') }}" method="GET">
                @if (request('lantai'))
                    <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                @endif

                <i class="bi bi-search absolute left-4 top-3.5 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor ruangan..."
                    class="w-full pl-12 pr-4 py-3 rounded-xl shadow-sm border border-gray-200 bg-white focus:ring-2 focus:ring-secondary focus:border-transparent outline-none transition duration-200 text-sm sm:text-base">
            </form>
        </div>
    </div>

    {{-- AI RECOMMENDATION BOX (collapsible) --}}
    @if(isset($rekomendasi) && !empty($rekomendasi['text']))
    <details class="group mx-2 sm:mx-4 md:mx-8 mb-8 bg-blue-50 border border-blue-200 rounded-2xl overflow-hidden">
        <summary class="list-none cursor-pointer p-4 sm:p-5 flex items-start sm:items-center gap-3 sm:gap-4 select-none">
            <div class="bg-blue-100 text-blue-600 p-2 sm:p-2.5 rounded-xl flex-shrink-0">
                <i class="bi bi-robot text-xl sm:text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                    <h3 class="font-semibold text-sm sm:text-base text-blue-900">Rekomendasi Ruangan</h3>
                    @if($rekomendasi['source'] === 'ai')
                        <span class="text-[10px] sm:text-xs bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full">Gemini AI</span>
                    @else
                        <span class="text-[10px] sm:text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Berbasis data histori</span>
                    @endif
                    @if(!empty($peringatan))
                        <span class="text-[10px] sm:text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                            <i class="bi bi-exclamation-triangle-fill"></i> {{ count($peringatan) }} ruangan padat
                        </span>
                    @endif
                </div>
                {{-- Preview teks sembunyi otomatis saat detail dibuka --}}
                <p class="text-xs sm:text-sm text-blue-700 truncate mt-1 group-open:hidden">
                    {{ \Illuminate\Support\Str::limit(str_replace(['Lantai Lantai', "\n"], ['Lantai', ' '], $rekomendasi['text']), 90) }}
                </p>
            </div>
            <div class="text-blue-500 text-xs sm:text-sm flex items-center gap-1 flex-shrink-0 mt-1 sm:mt-0">
                <span class="group-open:hidden">Lihat detail</span>
                <span class="hidden group-open:inline">Tutup</span>
                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
            </div>
        </summary>

        <div class="px-4 sm:px-5 pb-4 sm:pb-5 border-t border-blue-100 pt-4">
            {{-- Form filter kebutuhan (Kini fully responsive di Mobile, Tablet & Desktop) --}}
            <form method="GET" action="{{ route('ruangan.index') }}" class="flex flex-col lg:flex-row lg:items-center gap-4 xl:gap-5 mb-5 bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-blue-100 shadow-sm">
                @if(request('lantai'))
                    <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                {{-- Input Kapasitas Minimal --}}
                <div class="flex items-center justify-between sm:justify-start gap-3 w-full lg:w-auto">
                    <label class="text-xs font-semibold text-blue-900 whitespace-nowrap">Kapasitas minimal:</label>
                    <div class="inline-flex items-center bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-secondary focus-within:border-transparent transition">
                        <button type="button" onclick="decrementKapasitas()" 
                            class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 active:bg-gray-100 border-r border-gray-200 transition cursor-pointer select-none">
                            <i class="bi bi-dash text-sm font-bold"></i>
                        </button>
                        <input type="number" name="kebutuhan_kapasitas" id="inputKapasitas" min="0" step="5" value="{{ $kapasitasMin ?? '' }}"
                            placeholder="0"
                            class="w-12 text-center text-sm bg-transparent outline-none transition text-gray-700 font-semibold [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" onclick="incrementKapasitas()" 
                            class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 active:bg-gray-100 border-l border-gray-200 transition cursor-pointer select-none">
                            <i class="bi bi-plus text-sm font-bold"></i>
                        </button>
                    </div>
                </div>

                {{-- Indikator lantai aktif — ikut filter dropdown utama di luar, tidak ada kontrol terpisah di sini --}}
                <div class="flex items-center gap-2 w-full lg:w-auto text-xs text-blue-700">
                    <i class="bi bi-info-circle"></i>
                    <span>
                        Lantai:
                        <strong>{{ $namaLantaiRekomendasi ?? 'Semua lantai' }}</strong>
                        @if($namaLantaiRekomendasi)
                            <a href="{{ route('ruangan.index', array_filter(['search' => request('search'), 'kebutuhan_kapasitas' => $kapasitasMin])) }}"
                                class="text-blue-500 hover:underline ml-1">(lihat semua lantai)</a>
                        @endif
                    </span>
                </div>

                {{-- Tombol Aksi Form --}}
                <div class="flex items-center justify-end gap-2 w-full lg:w-auto lg:ml-auto pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                    <button type="submit" class="w-full sm:w-auto text-center text-sm bg-secondary hover:bg-blue-700 text-white font-medium px-5 py-1.5 rounded-lg shadow-sm transition cursor-pointer">
                        Sesuaikan
                    </button>
                    @if($kapasitasMin)
                        <a href="{{ route('ruangan.index', array_filter(['lantai' => request('lantai'), 'search' => request('search')])) }}"
                            class="text-center text-sm text-gray-500 hover:text-red-500 font-medium px-2 py-1.5 transition whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Narasi AI --}}
            <div class="prose prose-sm text-blue-800 leading-relaxed whitespace-pre-line mb-5 text-sm">
                {{ str_replace('Lantai Lantai', 'Lantai', $rekomendasi['text']) }}
            </div>

            {{-- Slot kosong per ruangan --}}
            @if(!empty($slotKosong))
            <div class="mb-5">
                <p class="text-[11px] font-bold text-blue-900 uppercase tracking-wide mb-2.5">Jam kosong terdekat</p>
                <div class="space-y-3">
                    @foreach ($slotKosong as $namaRuang => $slots)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                            <span class="text-sm font-semibold text-blue-900 sm:w-32 flex-shrink-0">{{ $namaRuang }}</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($slots as $slot)
                                    <span class="text-xs bg-white border border-blue-200 text-blue-700 px-2.5 py-1 rounded-full whitespace-nowrap shadow-sm">
                                        <i class="bi bi-clock mr-1"></i>{{ $slot }}
                                    </span>
                                @empty
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">Penuh</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Peringatan ruangan padat --}}
            @if(!empty($peringatan))
            <div>
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-wide mb-2.5">Peringatan ruangan padat</p>
                <div class="space-y-2">
                    @foreach ($peringatan as $namaRuang => $info)
                        <div class="text-xs bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded-xl leading-relaxed">
                            <i class="bi bi-exclamation-triangle-fill mr-1 text-amber-600"></i>
                            <strong>{{ $namaRuang }}</strong> okupansi {{ $info['occupancy'] }}% (7 hari ke depan).
                            @if($info['alternatif'])
                                Coba: <strong class="underline decoration-amber-400">{{ $info['alternatif'] }}</strong>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </details>
    @endif

    {{-- Grid Ruangan (Responsive Breakpoints dari Mobile ke Desktop XL) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mx-2 sm:mx-4 md:mx-8 gap-4 sm:gap-6">
        @forelse($semuaKelas as $kelas)
            <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3 gap-2">
                        <div class="min-w-0">
                            <h3 class="font-bold text-base sm:text-lg text-gray-800 group-hover:text-secondary transition truncate">
                                {{ $kelas->nama_kelas }}
                            </h3>
                            <p class="text-[10px] sm:text-xs text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded mt-1.5 inline-block">
                                {{ $kelas->lantai->nama_lantai }}
                            </p>
                        </div>
                        <i class="fa-solid fa-building text-gray-300 text-lg sm:text-xl flex-shrink-0 mt-1"></i>
                    </div>

                    <p class="text-gray-500 text-xs sm:text-sm mb-5 flex items-center">
                        <i class="bi bi-people mr-2 text-gray-400"></i> Kapasitas: <span class="font-semibold text-gray-700 ml-1">{{ $kelas->kapasitas }}</span>
                    </p>
                </div>

                <a href="{{ route('reservasi.create', ['class_id' => $kelas->id]) }}"
                    class="block w-full text-center bg-success hover:bg-green-600 text-white font-medium rounded-xl py-2.5 text-xs sm:text-sm transition shadow-sm hover:shadow-md cursor-pointer">
                    Booking Sekarang
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-16 sm:py-20 bg-white border border-gray-100 rounded-2xl mx-4">
                <div class="bg-gray-50 rounded-full w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-search text-2xl sm:text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Ruangan tidak ditemukan</h3>
                <p class="text-xs sm:text-sm text-gray-500 max-w-xs mx-auto mt-1">Coba cari kata kunci lain atau reset filter pencarian Anda.</p>
                <a href="{{ route('ruangan.index') }}" class="text-sm text-secondary font-medium hover:underline mt-3 inline-block">Reset Filter</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination Area --}}
    <div class="mt-8 mx-2 sm:mx-4 md:mx-8">
        {{ $semuaKelas->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    function incrementKapasitas() {
        const input = document.getElementById('inputKapasitas');
        let currentVal = parseInt(input.value) || 0;
        input.value = currentVal + 1;
    }

    document.addEventListener("DOMContentLoaded", () => {
        const btnLantai = document.getElementById("btnLantai");
        const dropdownLantai = document.getElementById("dropdownLantai");
        const lantaiWrapper = document.getElementById("lantaiWrapper");
        let open = false;

        function toggleDropdown() {
            open = !open;
            if (open) {
                dropdownLantai.classList.remove("hidden");
                setTimeout(() => {
                    dropdownLantai.classList.remove("opacity-0", "-translate-x-5", "sm:-translate-x-5");
                    dropdownLantai.classList.add("opacity-100", "translate-x-0");
                }, 10);
            } else {
                dropdownLantai.classList.remove("opacity-100", "translate-x-0");
                dropdownLantai.classList.add("opacity-0", "-translate-x-5", "sm:-translate-x-5");
                setTimeout(() => dropdownLantai.classList.add("hidden"), 300);
            }
        }

        if(btnLantai) {
            btnLantai.addEventListener("click", (e) => {
                e.stopPropagation();
                toggleDropdown();
            });
        }

        document.addEventListener("click", (e) => {
            if (lantaiWrapper && !lantaiWrapper.contains(e.target) && open) {
                toggleDropdown();
            }
        });
    });

    function decrementKapasitas() {
        const input = document.getElementById('inputKapasitas');
        let currentVal = parseInt(input.value) || 0;
        if (currentVal > 0) {
            input.value = currentVal - 1;
        } else {
            input.value = '';
        }
    }
</script>
@endpush
@endsection