<?php

namespace App\Http\Controllers;

// use Dotenv\Validator;

use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Models\UserModel;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }
        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function register() {
        $levels = LevelModel::all();

        return view ('auth.register', compact('levels'));
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|min:4|unique:m_user,username',
        'nama' => 'required|string|max:100',
        'password' => 'required|min:5',
        'level_id' => 'required|exists:m_level,level_id',
        
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi Gagal!',
            'msgField' => $validator->errors()
        ], 422);
    }

    $user = UserModel::create([
        'username' => $request->username,
        'nama' => $request->nama,
        'password' => bcrypt($request->password), 
        'level_id' => $request->level_id,
    ]);

    Auth::login($user);

    return response()->json([
        'status' => true,
        'message' => 'Registrasi Berhasil!',
        'redirect' => url('/')
    ], 200);
}
}