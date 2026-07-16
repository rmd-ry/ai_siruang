<x-filament-panels::page>
    {{-- Section Header Custom --}}
    <x-filament::section>
    {{-- LAYOUT UTAMA: Pake style="display: flex" biar pasti sebaris --}}
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">

        {{-- BAGIAN KIRI (Avatar + Teks): Flex juga biar sebelahan --}}
        <div style="display: flex; align-items: center; gap: 1rem;">

            {{-- Avatar --}}
            <x-filament::avatar
                :src="filament()->getUserAvatarUrl(auth()->user())"
                alt="Avatar User"
                class="w-12 h-12"
                style="width: 3rem; height: 3rem; border-radius: 9999px;"
            />

            {{-- Teks --}}
            <div>
                <h2 class="text-lg font-bold" style="margin-bottom: 0; line-height: 1.2;">
                    Selamat Datang
                </h2>
                <p class="text-sm text-gray-500" style="margin-top: 0;">
                    {{ auth()->user()->nama ?? 'AdminSira' }}
                </p>
            </div>
        </div>

        {{-- BAGIAN KANAN (Tombol Keluar) --}}
        <form action="{{ filament()->getLogoutUrl() }}" method="post" style="margin: 0;">
            @csrf
            <x-filament::button
                color="gray"
                icon="heroicon-m-arrow-left-on-rectangle"
                type="submit"
                outlined
            >
                Keluar
            </x-filament::button>
        </form>

    </div>
</x-filament::section>

    {{-- Section Widget (Pake cara Native Filament) --}}
    <x-filament-widgets::widgets :columns="$this->getColumns()" :data="$this->getWidgetData()" :widgets="$this->getVisibleWidgets()" />
</x-filament-panels::page>
