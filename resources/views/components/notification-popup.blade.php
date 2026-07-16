@php
    $isNotifPage = request()->routeIs('notifikasi.index');
@endphp

<div data-dropdown class="relative">

    <!-- Tombol Notifikasi -->
    <button type="button" data-dropdown-trigger class="relative flex items-center py-2 px-5 rounded-md transition-200
        {{ $isNotifPage
            ? 'bg-white text-secondary shadow-sm'
            : 'text-gray-300 hover:bg-gray-200/20 hover:text-gray-200' }}">

        <i class="bi bi-bell-fill text-xl"></i>

        <span
            class="absolute top-1 right-3 bg-red-500 text-white text-[10px] px-1.5 rounded-full">{{ $notifUnreadCount }}</span>
    </button>

    <!-- Popup -->
    <div data-dropdown-panel
        data-enter-class="opacity-100"
        data-leave-class="opacity-0"
        data-close-delay="200"
        class="fixed left-3 right-3 top-16 sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-[450px] w-auto max-w-full sm:max-w-[90vw] bg-white rounded-xl shadow-lg z-[60] overflow-hidden hidden opacity-0 transition-opacity duration-200">

        <!-- Header -->
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <h4 class="font-semibold text-gray-800">Pemberitahuan</h4>
            <form action="{{ route('notifikasi.clear') }}" method="POST">
                @csrf

                <button type="submit" class="text-sm text-blue-500 hover:underline">
                    Tandai Semua Dibaca
                </button>
            </form>
        </div>

        <hr class="border-gray-200">

        <!-- List -->
        <div class="max-h-[45vh] sm:max-h-[300px] overflow-y-auto">
            @forelse($notifPopup as $item)
                @php
                    $isApproved = $item->status === \App\StatusReservasi::DITERIMA;
                    $isUnread = is_null($item->read_at);
                @endphp

                <div class="px-4 py-3 {{ $isUnread ? 'bg-gray-100' : '' }}">
                    <p class="font-semibold text-sm {{ $isApproved ? 'text-success' : 'text-error' }}">
                        {{ $isApproved ? 'Reservasi Disetujui!' : 'Reservasi Ditolak!' }}
                    </p>

                    <p class="text-xs text-gray-600">
                        <i class="bi bi-building-fill mr-2"></i>
                        {{ $item->kelas->nama_kelas }}
                    </p>

                    <p class="text-xs text-gray-600 flex justify-between">
                        <span>
                            <i class="bi bi-calendar3 mr-1"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}
                        </span>
                        <span>
                            <i class="bi bi-clock mr-1"></i>
                            {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                        </span>
                    </p>
                </div>

                <hr class="border-gray-200">
            @empty
                <div class="py-10 text-center text-gray-400 text-sm">
                    Tidak ada notifikasi
                </div>
            @endforelse
        </div>

        <hr class="border-gray-200">

        <!-- Footer -->
        <div class="border-t px-4 py-3 text-center">
            <a href="{{ route('notifikasi.index') }}" class="text-sm text-blue-500 hover:underline">
                Lihat Semua
            </a>
        </div>
    </div>
</div>