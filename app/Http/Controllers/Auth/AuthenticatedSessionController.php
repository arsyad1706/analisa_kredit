<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $user = User::select('email','nip', 'password', 'role','user_dagulir')
                    ->where('email', $request->email)
                    ->orWhere('nip', $request->email)
                    ->first();
        $device_name = gethostname();

        if ($user) {
            if ($user->role == 'Administrator' || $user->role == 'Direksi' || $user->user_dagulir == 1) {
                if (\Hash::check($request->password, $user->password)) {
                    $request->authenticate();

                    if (DB::table('sessions')->where('user_id', auth()->user()->id)->count() > 0) {
                        Auth::guard('web')->logout();

                        $request->session()->invalidate();

                        $request->session()->regenerateToken();
                        return back()->withError("Akun sedang digunakan di perangkat lain.");
                    }

                    $request->session()->regenerate();

                    // Set device name
                    DB::table('sessions')
                        ->where('user_id', auth()->user()->id)
                        ->update([
                            'device_name' => $device_name
                        ]);

                    if ($user->role == 'Direksi') {
                        return redirect()->route('dashboard_direksi');
                    }

                    return redirect()->intended(RouteServiceProvider::HOME);
                } else {
                    return back()->withError("Password yang anda masukan salah");
                }
            } else {
                if (isset($user->nip)) {
                    if (\Hash::check($request->password, $user->password)) {
                        $request->authenticate();
                        if (DB::table('sessions')->where('user_id', auth()->user()->id)->count() > 0) {
                            Auth::guard('web')->logout();

                            $request->session()->invalidate();

                            $request->session()->regenerateToken();
                            return back()->withError("Akun sedang digunakan di perangkat lain.");
                        }
                        // Set device name
                        DB::table('sessions')
                            ->where('id', Session::getId())
                            ->update([
                                'device_name' => $device_name
                            ]);

                        $request->session()->regenerate();


                        return redirect()->intended(RouteServiceProvider::HOME);
                    } else {
                        return back()->withError("Password yang anda masukan salah");
                    }
                } else {
                    return back()->withError("Belum dilakukan Pengkinian Data User untuk $request->email.\nHarap menghubungi Divisi Pemasaran atau TI & AK.");
                }
            }
        }
        else {
            return back()->withError("Akun tidak ditemukan");
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // session authsession token onedrive
        session()->forget('accessToken');
        session()->forget('refreshToken');
        session()->forget('tokenExpires');
        return redirect('/');
    }
}
