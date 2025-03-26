<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level']
        ];  
        $page = (object) [
            'title' => 'Daftar level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level'; // Untuk indikator menu yang sedang aktif

        // Ambil semua data level
        $level = LevelModel::all(); 

        return view('level.index', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'level' => $level, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return DataTables::of($level)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                $btn  = '<a href="'.url('/level/' . $level->level_id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/level/' . $level->level_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/level/'.$level->level_id).'">'
                        . csrf_field() . method_field('DELETE') .  
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">
                            Hapus
                        </button>
                    </form>';      

                return $btn; 
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home', 'Level', 'Tambah']
        ];  
        $page = (object) [
            'title' => 'Tambah level baru'
        ];

        $activeMenu = 'level'; // Untuk indikator menu yang sedang aktif

        return view('level.create', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required|string|max:10', // Added string validation and max length
            'level_nama' => 'required|string|max:100' // Added string validation and max length
        ]);
    
        LevelModel::create([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama
        ]);
    
        return redirect('/level')->with('status', 'Data level berhasil ditambahkan');
    }

    public function show($id)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Level',
            'list' => ['Home', 'Level', 'Detail']
        ];  
        $page = (object) [
            'title' => 'Detail level'
        ];

        $activeMenu = 'level'; // Untuk indikator menu yang sedang aktif

        $level = LevelModel::find($id);

        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        return view('level.show', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'level' => $level, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit(string $id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];  
        $page = (object) [
            'title' => 'Edit level'
        ];

        $activeMenu = 'level'; // Untuk indikator menu yang sedang aktif

        $level = LevelModel::find($id);

        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        return view('level.edit', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'level' => $level, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_nama' => 'required|string|max:100' // Added string validation and max length
        ]);
    
        $level = LevelModel::find($id);

        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $level->update([
            'level_nama' => $request->level_nama
        ]);
    
        return redirect('/level')->with('status', 'Data level berhasil diubah');
    }

    public function destroy($id)
    {
        $level = LevelModel::find($id);

        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $level->delete();
        return redirect('/level')->with('status', 'Data level berhasil dihapus');
    }

    public function create_ajax() {
        return view('level.create_ajax'); 
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|min:3|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:100', // nama maksimal 100 karakter
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            LevelModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data level berhasil disimpan'
            ]);
        }

        return redirect('/level'); // Adjust the redirect path as necessary
    }

    public function edit_ajax(string $id) {
        $level = LevelModel::find($id);
        return view('level.edit_ajax', ['level' => $level]); // Adjust the view path as necessary
    }

    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|min:3|unique:m_level,level_kode,'.$id.',level_id',
                'level_nama' => 'required|string|max:100', // nama maksimal 100 karakter
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            $level = LevelModel::find($id);
            if ($level) {
                $level->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil diubah'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }
        return redirect('/level'); // Adjust the redirect path as necessary
    }

    public function confirm_ajax(string $id) {
        $level = LevelModel::find($id);
        return view('level.confirm_ajax', ['level' => $level]); // Adjust the view path as necessary
    }

    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $level = LevelModel::find($id);
            if ($level) {
                $level->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }
        return redirect('/level'); // Adjust the redirect path as necessary
    }
}