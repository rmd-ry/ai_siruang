<nav x-data="{ openMobileMenu: false, openMobileConfirm: false }" class="bg-[#063970] text-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            {{-- Kiri: Logo --}}
            <div class="flex items-center">
                <a href="/" class="text-2xl sm:text-3xl font-bold tracking-wider text-white select-none">
                    SIRuang
                </a>
            </div>
            
            {{-- Tengah: Menu Utama Desktop (Hanya dipisah agar otomatis berada di tengah) --}}
            <div class="hidden lg:flex flex-1 justify-center items-center gap-2">
                <a href="/" 
                    class="px-4 py-2 rounded-xl text-sm transition duration-200 {{ request()->is('/') ? 'bg-white text-[#063970] font-semibold shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    Beranda
                </a>
                <a href="{{ route('ruangan.index') }}" 
                    class="px-4 py-2 rounded-xl text-sm transition duration-200 {{ request()->routeIs('ruangan.*', 'reservasi.*') ? 'bg-white text-[#063970] font-semibold shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    Reservasi
                </a>
                <a href="/riwayat" 
                    class="px-4 py-2 rounded-xl text-sm transition duration-200 {{ request()->routeIs('riwayat') ? 'bg-white text-[#063970] font-semibold shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    Riwayat
                </a>
            </div>

            {{-- Kanan: Notifikasi, Akun, & Logout Desktop --}}
            <div class="hidden lg:flex items-center gap-4">
                <x-notification-popup />
                
                <div class="w-px h-6 bg-white/20"></div>

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 py-2 px-3 rounded-xl text-sm transition duration-200 {{ request()->routeIs('profile.*') ? 'bg-white text-[#063970] font-semibold' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="bi bi-person-circle text-base"></i>
                    <span class="max-w-[80px] md:max-w-[120px] truncate" title="{{ auth()->user()->nama ?? 'Tamu' }}">
                        {{ auth()->user()->nama ?? 'Tamu' }}
                    </span>
                </a>

                <div class="w-px h-6 bg-white/20"></div>

                <div id="logoutWrapper" class="relative">
                    <x-logout-dropdown />
                </div>
            </div>

            {{-- Hamburger Menu & Notifikasi Mini (Mobile/Tablet) --}}
            <div class="flex lg:hidden items-center gap-2">
                <x-notification-popup />

                <button @click="openMobileMenu = ! openMobileMenu" type="button" 
                    class="text-gray-300 p-2 rounded-xl hover:bg-white/10 hover:text-white transition focus:outline-none cursor-pointer">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': openMobileMenu, 'inline-flex': ! openMobileMenu }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! openMobileMenu, 'inline-flex': openMobileMenu }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Menu Dropdown Laci khusus Mobile/Tablet --}}
    <div x-show="openMobileMenu" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden border-t border-white/10 bg-[#063970] px-5 pt-3 pb-6 space-y-4 shadow-inner">
         
        <div class="flex flex-col gap-1">
            <a href="/" 
                class="block w-full px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->is('/') ? 'bg-white text-[#063970] shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-house-door mr-3 opacity-70"></i>Beranda
            </a>
            <a href="{{ route('ruangan.index') }}" 
                class="block w-full px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('ruangan.*', 'reservasi.*') ? 'bg-white text-[#063970] shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-building mr-3 opacity-70"></i>Reservasi
            </a>
            <a href="/riwayat" 
                class="block w-full px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('riwayat') ? 'bg-white text-[#063970] shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-clock-history mr-3 opacity-70"></i>Riwayat
            </a>
        </div>
        
        <div class="border-t border-white/10 pt-4 flex flex-col gap-3">
            <a href="{{ route('profile.edit') }}" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('profile.*') ? 'bg-white text-[#063970]' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-person-circle text-xl text-gray-300"></i>
                <div class="flex flex-col min-w-0">
                    <span class="truncate font-semibold text-white">{{ auth()->user()->nama ?? 'Tamu' }}</span>
                    <span class="text-[11px] text-gray-400 truncate">Lihat Profil &rarr;</span>
                </div>
            </a>
            
            <div class="px-2 pt-1">
                <button @click="openMobileConfirm = true" type="button" class="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-3 rounded-xl text-sm shadow transition cursor-pointer">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar Aplikasi</span>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI KELUAR UNTUK MOBILE --}}
    <div x-show="openMobileConfirm" 
         class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div @click.outside="openMobileConfirm = false" 
             class="bg-white text-gray-800 w-full max-w-sm p-6 rounded-2xl shadow-2xl transform transition-all flex flex-col items-center text-center"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
             
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4">
                <i class="bi bi-exclamation-triangle text-2xl"></i>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 mb-1">Konfirmasi Keluar</h3>
            <p class="text-gray-500 text-sm mb-6">Apakah Anda yakin ingin keluar dari aplikasi SIRuang?</p>
            
            <div class="flex w-full gap-3">
                <button @click="openMobileConfirm = false" type="button" 
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition text-sm cursor-pointer">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition text-sm cursor-pointer">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>