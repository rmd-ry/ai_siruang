<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar - SIRuang</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- Google Font Inter --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/script.js'])
</head>

<body class="bg-gray-200 min-h-screen flex items-center justify-center bg-cover bg-center font-inter py-8"
    style="background-image: url('/images/bg-loginn.jpg'); background-repeat: no-repeat;">

    <div class="absolute inset-0 bg-primary/75"></div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-8 lg:px-16 flex flex-col lg:flex-row justify-between items-center gap-8 lg:gap-6 py-8">
        {{-- Side Left --}}
        <div class="w-full lg:w-1/2 lg:ms-6 xl:ms-16 text-center lg:text-left">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl text-white font-bold mb-3">SIRuang</h1>
            <p class="text-white text-sm sm:text-base lg:text-lg max-w-md mx-auto lg:mx-0">
                Sistem Informasi Ruangan Universitas Yatsi Madani
            </p>
        </div>

        {{-- Side right --}}
        <div class="w-full max-w-md lg:w-1/3 lg:me-6 xl:me-16">
            <div class="bg-white p-6 sm:p-8 rounded-xl border">
                <h2 class="text-xl sm:text-2xl font-bold mb-6">DAFTAR AKUN</h2>

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required
                        class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('nama')
                        <small class="text-red-500 mb-2 block">{{ $message }}</small>
                    @enderror

                    {{-- NIM --}}
                    <label class="block text-sm font-medium mb-1 mt-3">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim') }}" placeholder="Masukkan NIM" required
                        class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('nim')
                        <small class="text-red-500 mb-2 block">{{ $message }}</small>
                    @enderror

                    {{-- Username --}}
                    <label class="block text-sm font-medium mb-1 mt-3">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Buat username" required
                        class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('username')
                        <small class="text-red-500 mb-2 block">{{ $message }}</small>
                    @enderror

                    {{-- Email (opsional) --}}
                    <label class="block text-sm font-medium mb-1 mt-3">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email"
                        class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('email')
                        <small class="text-red-500 mb-2 block">{{ $message }}</small>
                    @enderror

                    {{-- Program Studi (opsional) --}}
                    <label class="block text-sm font-medium mb-1 mt-3">Program Studi <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" name="program_studi" value="{{ old('program_studi') }}" placeholder="Contoh: Teknik Informatika"
                        class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('program_studi')
                        <small class="text-red-500 mb-2 block">{{ $message }}</small>
                    @enderror

                    {{-- Password --}}
                    <label class="block text-sm font-medium mb-1 mt-3">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Buat kata sandi" required
                            class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                            <i id="eyeIcon" class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                    @error('password')
                        <small class="text-red-500 mb-2 block">{{ $message }}</small>
                    @enderror

                    {{-- Konfirmasi Password --}}
                    <label class="block text-sm font-medium mb-1 mt-3">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required
                        class="w-full bg-white shadow-md rounded-md px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">

                    <button type="submit"
                        class="w-full bg-primary cursor-pointer text-white py-2 rounded-md hover:bg-secondary transition duration-200">
                        Daftar
                    </button>

                    <p class="text-center text-xs text-gray-500 mt-4">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Login di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>