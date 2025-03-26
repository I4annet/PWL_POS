<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];  
        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori'; // Untuk indikator menu yang sedang aktif

        // Ambil semua data kategori
        $kategori = KategoriModel::all(); 

        // Pass the kategori data to the view
        return view('kategori.index', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'activeMenu' => $activeMenu,
            'kategori' => $kategori // Pass the kategori variable here
        ]);
    }

    // Ambil data kategori dalam bentuk JSON untuk DataTables
    public function list(Request $request) { 
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {  
                $btn  = '<a href="'.url('/kategori/' . $kategori->id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/kategori/' . $kategori->id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/kategori/'.$kategori->id).'">'
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

    // Menampilkan halaman form tambah kategori
    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Tambah']
        ];  
        $page = (object) [
            'title' => 'Tambah kategori baru'
        ];

        $activeMenu = 'kategori'; // Untuk indikator menu yang sedang aktif

        $kategori = KategoriModel::all(); // Ambil semua data kategori

        return view('kategori.create', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'activeMenu' => $activeMenu,
            'kategori' => $kategori 
        ]);
    }

    // Menyimpan data kategori baru
    public function store(Request $request) {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100', // nama maksimal 100 karakter
        ]);

        KategoriModel::create([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);
        return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
    }

    public function show(string $id) {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list' => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail kategori'
        ];

        $activeMenu = 'kategori'; // Untuk indikator menu yang sedang aktif

        return view('kategori.show', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'kategori' => $kategori, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit(string $id) {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit kategori'
        ];

        $activeMenu = 'kategori'; // Untuk indikator menu yang sedang aktif

        return view('kategori.edit', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'kategori' => $kategori, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,'.$id.',id',
            'kategori_nama' => 'required|string|max:100', // nama maksimal 100 karakter
        ]);

        KategoriModel::find($id)->update([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);
        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
    }

    public function destroy(string $id) {
        $check = KategoriModel::find($id);
        if (!$check) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        try {
            KategoriModel::destroy($id);
            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/kategori')->with('error', 'Data kategori tidak bisa dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax() {
        return view('kategori.create_ajax'); // Adjust the view path as necessary
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100', // nama maksimal 100 karakter
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            KategoriModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil disimpan'
            ]);
        }

        return redirect('/kategori'); // Adjust the redirect path as necessary
    }

    public function edit_ajax(string $id) {
        $kategori = KategoriModel::find($id);
        return view('kategori.edit_ajax', ['kategori' => $kategori]); 
    }

    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,'.$id.',id',
                'kategori_nama' => 'required|string|max:100', // nama maksimal 100 karakter
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            $kategori = KategoriModel::find($id);
            if ($kategori) {
                $kategori->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil diubah'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data kategori tidak ditemukan'
                ]);
            }
        }
        return redirect('/'); // Adjust the redirect path as necessary
    }

    public function confirm_ajax(string $id) {
        $kategori = KategoriModel::find($id);
        return view('kategori.confirm_ajax', ['kategori' => $kategori]); // Adjust the view path as necessary
    }

    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = KategoriModel::find($id);
            if ($kategori) {
                $kategori->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data kategori tidak ditemukan'
                ]);
            }
        }
        return redirect('/kategori'); // Adjust the redirect path as necessary
    }
}