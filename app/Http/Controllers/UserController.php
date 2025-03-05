<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
public function index() {

      $user = UserModel::all();
      return view('user', ['data' => $user]);
    }

public function tambah() {
        return view('user_tambah');
    }


public function tambah_simpan(Request $request) {
    UserModel::create([
        'username' => $request->username,   
        'nama' => $request->nama,
        'password' => Hash::make($request->password),
        'level_id' => $request->level_id
    ]); 

    return redirect('/user');   
    }
}