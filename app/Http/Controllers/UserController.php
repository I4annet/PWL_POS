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

        $level = LevelModel::all(); // Ambil semua data level

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }
    // Ambil data user dalam bentuk json untuk datatables 
    public function list(Request $request) 
    { 
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
                    ->with('level');
        
        // Filter data user berdasarkan level_id
        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }
    
        return DataTables::of($users)
            // Menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {  
                // Menambahkan kolom aksi
                $btn  = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/user/'.$user->user_id).'">'
                        . csrf_field() . method_field('DELETE') .  
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">
                            Hapus
                        </button>
                    </form>';      
    
                return $btn; 
            })
            ->rawColumns(['aksi']) // Memberitahu bahwa kolom aksi adalah HTML
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

    public function show(string $id) {
        $user = UserModel::with('level')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail user'
        ];

        $activeMenu = 'user'; // Untuk indikator menu yang sedang aktif

        return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id) {
        $user = UserModel::find($id);
        $level = LevelModel::all(); // Ambil semua data level

        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list' => ['Home', 'User', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit user'
        ];

        $activeMenu = 'user'; // Untuk indikator menu yang sedang aktif

        return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'username' => 'required|string|min:3|unique:m.user,username,'.$id.',user_id',
            'nama' => 'required|string|max:100', // nama maksimal 100 karakter
            'password' => 'nullable|min:5', // password minimal 5 karakter
            'level_id' => 'required|integer'  // level_id harus berupa angka
        ]);

        UserModel::find($id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id' => $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    public function destroy(string $id) {
        $check = UserModel::find($id);
        if (!$check) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        try {
            UserModel::destroy($id);
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/user')->with('error', 'Data user tidak bisa dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

}

