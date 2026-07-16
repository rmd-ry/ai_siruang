<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50', 'unique:' . User::class . ',nim'],
            'username' => ['required', 'string', 'max:50', 'unique:' . User::class . ',username'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'username' => $request->username,
            'email' => $request->email,
            'program_studi' => $request->program_studi,
            'password' => Hash::make($request->password),
            'role' => UserRole::USER,
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Registrasi berhasil. Silakan login menggunakan akun Anda.');
    }
}