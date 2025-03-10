<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel;

class UserController extends Controller
{
public function index() {
    $breadcrumb = (object) [
        'title' => 'Daftar User',
        'list' => ['Home', 'User']
    ];  
        $page = (object) [
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];


        $activeMenu = 'user'; // Untuk indikator menu yang sedang aktif

        return view('user.index', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 'page' => $page]);
    }
    // Ambil data user dalam bentuk json untuk datatables 
public function list(Request $request) 
{ 
    $users = UserModel::select('user_id', 'username', 'nama', 'level_id') 
                ->with('level'); 
 
    return DataTables::of($users) 
        // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
        ->addIndexColumn()  
        ->addColumn('aksi', function ($user) {  // menambahkan kolom aksi 
            $btn  = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn
sm">Detail</a> '; 
            $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn
warning btn-sm">Edit</a> '; 
            $btn .= '<form class="d-inline-block" method="POST" action="'. 
url('/user/'.$user->user_id).'">' 
                    . csrf_field() . method_field('DELETE') .  
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return 
confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';      
            return $btn; 
        }) 
        ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
        ->make(true); 
}

    // Menampilkan halaman form tambah user
    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];  
        $page = (object) [
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all(); // Ambil semua data level
        $activeMenu = 'user'; // Untuk indikator menu yang sedang aktif

        return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan data user baru
    public function store(Request $request) {
        $request->validate([
            'username' => 'required|string|min:3|unique:m.user,username',
            'nama' => 'required|string|max:100', // nama maksimal 100 karakter
            'password' => 'required|min:5', // password minimal 5 karakter
            'level_id' => 'required|integer'  // level_id harus berupa angka
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }

}

