@extends('layouts.app')

@section('content')
    <div class="px-3 sm:px-6 py-6 sm:py-10">

        <div class="bg-white p-5 sm:p-8 rounded-2xl sm:rounded-3xl shadow-md w-full max-w-4xl mx-auto">

            <h1 class="text-lg sm:text-xl font-semibold mb-2">Profil Akun</h1>
            <p class="text-xs sm:text-sm text-gray-500 mb-5 sm:mb-6">
                Lengkapi username, email, dan program studi kamu di sini.
            </p>

            @if (session('status') === 'profile-updated')
                <div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2.5 rounded-lg">
                    <i class="bi bi-check-circle-fill mr-1"></i> Profil berhasil diperbarui.
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 sm:gap-y-5 gap-x-6 sm:gap-x-10">

                    {{-- NIM: read-only, identitas tetap --}}
                    <div class="min-w-0">
                        <label class="text-gray-600 text-xs sm:text-sm block mb-1">NIM</label>
                        <p class="font-semibold text-sm sm:text-base break-words bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-gray-500">
                            {{ $user->nim ?? '-' }}
                        </p>
                    </div>

                    {{-- Nama --}}
                    <div class="min-w-0">
                        <label for="nama" class="text-gray-600 text-xs sm:text-sm block mb-1">Nama</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                        @error('nama')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="min-w-0">
                        <label for="username" class="text-gray-600 text-xs sm:text-sm block mb-1">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                        @error('username')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="min-w-0">
                        <label for="email" class="text-gray-600 text-xs sm:text-sm block mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            placeholder="Belum diisi"
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                        @error('email')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Program Studi --}}
                    <div class="min-w-0">
                        <label for="program_studi" class="text-gray-600 text-xs sm:text-sm block mb-1">Program Studi</label>
                        <input type="text" id="program_studi" name="program_studi" value="{{ old('program_studi', $user->program_studi) }}"
                            placeholder="Belum diisi"
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                        @error('program_studi')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <label for="Angkatan" class="text-gray-600 text-xs sm:text-sm block mb-1">Angkatan</label>
                        <input type="number" id="Angkatan" name="Angkatan" value="{{ old('Angkatan', $user->Angkatan) }}"
                            placeholder="Contoh: 2024"
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                        @error('Angkatan')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <label for="agama" class="text-gray-600 text-xs sm:text-sm block mb-1">Agama</label>
                        <input type="text" id="agama" name="agama" value="{{ old('agama', $user->agama) }}"
                            placeholder="Belum diisi"
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                        @error('agama')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <label for="jenis_kelamin" class="text-gray-600 text-xs sm:text-sm block mb-1">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                            class="w-full text-sm sm:text-base rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
                            <option value="" @selected(old('jenis_kelamin', $user->jenis_kelamin) === null)>Belum dipilih</option>
                            <option value="Laki-laki" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki')>Laki-laki</option>
                            <option value="Perempuan" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan')>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <p class="text-gray-600 text-xs sm:text-sm">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs sm:text-sm bg-green-100 text-green-700">
                            Aktif
                        </span>
                    </div>

                </div>

                <button type="submit"
                    class="mt-6 sm:mt-8 bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-sm hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>

    </div>
@endsection