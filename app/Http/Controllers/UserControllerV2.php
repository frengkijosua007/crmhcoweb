<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserControllerV2 extends Controller
{
    // Hapus konstruktor yang menggunakan middleware

    /**
     * Menampilkan daftar pengguna.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Tambahkan kode method index asli Anda di sini
        // ...
    }

    /**
     * Menampilkan profil pengguna.
     */
    public function profile()
    {
        return view('users.profile', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Memperbarui profil pengguna.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui');
    }

    // Tambahkan method controller lainnya di sini
    // ...
}
