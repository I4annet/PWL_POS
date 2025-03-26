<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;   

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
                // $btn  = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="'.url('/user/'.$user->user_id).'">'
                //         . csrf_field() . method_field('DELETE') .  
                //         '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">
                //             Hapus
                //         </button>
                //     </form>';      
                
                $btn  = '<button onclick="modalAction(\''.url('/user/' . $user->user_id . 
                '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . 
                '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . 
                '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn; 
            })
            ->rawColumns(['aksi']) 
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
            'username' => 'required|string|min:3|unique:m_user,username',
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

    public function create_ajax() {

        return view('user.create_ajax')->with('level', LevelModel::select('level_id', 'level_nama')->get());
    }

public function store_ajax(Request $request) {
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
        'username' => 'required|string|min:3|unique:m_user,username',
        'nama' => 'required|string|max:100', // nama maksimal 100 karakter
        'password' => 'required|min:6', // password minimal 5 karakter
        'level_id' => 'required|integer'  // level_id harus berupa angka
        ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi Gagal',
            'msgfield' => $validator->errors(),
        ]);
    }

    UserModel::create($request->all());
    return response()->json([
        'status' => true,
        'message' => 'Data user berhasil disimpan'
    ]);
   
    }

    redirect('/user');
    }

    public function edit_ajax(string $id) {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get(); // Ambil semua data level

        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'username' => 'required|string|min:3|unique:m_user,username,'.$id.',user_id',
                'nama' => 'required|string|max:100', // nama maksimal 100 karakter
                'password' => 'nullable|min:6', // password minimal 5 karakter
                'level_id' => 'required|integer'  // level_id harus berupa angka
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }
            
            $check = UserModel::find($id);
            if ($check) {
                if(!$request->filled('password')) {
                    $request->request->remove('password');
                }

                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data user berhasil diubah'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data user tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id) {
        return view('user.confirmajax', ['user' => UserModel::find($id)]);   
    }

    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);
            if ($user) {
                $user->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data user berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data user tidak ditemukan'
                ]);
            }
        }
        return redirect('/user');
    }
}

