<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // ================= GOOGLE LOGIN =================

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();

            $registeredUser = User::where('email', $socialUser->getEmail())->first();

            if (!$registeredUser) {
                $user = User::create([
                    'nama' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'role' => '2',
                    'status' => 1,
                    'password' => Hash::make('default_password'),
                ]);

                Customer::create([
                    'user_id' => $user->id,
                    'google_id' => $socialUser->getId(),
                    'google_token' => $socialUser->token,
                ]);

                Auth::login($user);
            } else {
                $customer = Customer::where('user_id', $registeredUser->id)->first();
                if ($customer) {
                    $customer->update([
                        'google_id' => $socialUser->getId(),
                        'google_token' => $socialUser->token,
                    ]);
                } else {
                    Customer::create([
                        'user_id' => $registeredUser->id,
                        'google_id' => $socialUser->getId(),
                        'google_token' => $socialUser->token,
                    ]);
                }
                Auth::login($registeredUser);
            }

            return redirect()->intended('beranda');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ================= AKUN CUSTOMER =================

    public function akun($id)
    {
        $customer = Customer::where('user_id', $id)->firstOrFail();

        return view('v_customer.edit', [  // ← pastikan ini, bukan 'frontend.akun'
            'judul' => 'Akun Customer',
            'edit' => $customer,
        ]);
    }

    public function updateAkun(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if ($customer) {
            $customer->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'hp' => $request->hp,
                'alamat' => $request->alamat,
                'pos' => $request->pos,
            ]);
        }

        return back()->with('success', 'Data berhasil diperbarui');
    }

    // ================= LOGOUT =================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function index()
    {
        $customer = Customer::orderBy('id', 'desc')->get();
        return view('backend.v_customer.index', [
            'judul' => 'Customer',
            'sub' => 'Halaman Customer',
            'index' => $customer,
        ]);
    }

    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.v_customer.edit', [
            'judul' => 'Ubah Customer',
            'sub' => 'Halaman Ubah Customer',
            'edit' => $customer,
        ]);
    }

}