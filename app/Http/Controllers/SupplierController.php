<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel;
use App\Models\KategoriModel;
use App\Models\BarangModel;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];  
        $page = (object) [
            'title' => 'Daftar Supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier'; // Untuk indikator menu yang sedang aktif

        $supplier = SupplierModel::all(); // Ambil semua data supplier

        return view('supplier.index', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'supplier' => $supplier, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request) 
    { 
        $supplier = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');
    
        return DataTables::of($supplier)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {  
                $btn  = '<a href="'.url('/supplier/' . $supplier->supplier_id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/supplier/' . $supplier->supplier_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/supplier/'.$supplier->supplier_id).'">'
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
    

    // Menampilkan halaman form tambah supplier
    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah']
        ];  
        $page = (object) [
            'title' => 'Tambah supplier baru'
        ];

        $activeMenu = 'supplier'; // Untuk indikator menu yang sedang aktif

        $supplier = SupplierModel::all(); // Ambil semua data supplier

        return view('supplier.create', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'activeMenu' => $activeMenu,
            'supplier' => $supplier
        ]);
    }

    // Menyimpan data supplier baru
    public function store(Request $request) {
        $request->validate([
            'supplier_kode' => 'required|string|min:3|unique:m_supplier,supplier_kode', // supplier_kode minimal 3 karakter dan bersifat unik
            'nama_supplier' => 'required|string|max:100', // nama maksimal 100 karakter
        ]);

        SupplierModel::create([
            'supplier_kode' => $request->supplier_kode,
            'nama_supplier' => $request->nama_supplier,
        ]);
        return redirect('/supplier')->with('success', 'Data Supplier berhasil disimpan');
    }

    public function show(string $id) {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail supplier'
        ];

        $activeMenu = 'supplier'; // Untuk indikator menu yang sedang aktif

        return view('supplier.show', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'supplier' => $supplier, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit(string $id) {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit supplier'
        ];

        $activeMenu = 'supplier'; // Untuk indikator menu yang sedang aktif

        return view('supplier.edit', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'supplier' => $supplier, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'supplier_kode' => 'required|string|min:3|unique:m_supplier,supplier_kode,'.$id.',id', // Adjusted for unique validation
            'supplier_nama' => 'required|string|max:100', // nama maksimal 100 karakter
        ]);

        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $supplier->update([
            'supplier_kode' => $request->supplier_kode,
            'nama_supplier' => $request->nama_supplier,
        ]);
        return redirect('/supplier')->with('success', 'Data supplier berhasil diubah');
    }

    public function destroy(string $id) {
        $check = SupplierModel::find($id);
        if (!$check) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            SupplierModel::destroy($id);
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier tidak bisa dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax() {
        return view('supplier.create_ajax'); 
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_kode' => 'required|string|min:3|unique:m_supplier,supplier_kode',
                'supplier_nama' => 'required|string|max:100', 
                'supplier_alamat' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            SupplierModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data supplier berhasil disimpan'
            ]);
        }

        return redirect('/supplier'); // Adjust the redirect path as necessary
    }

    public function edit_ajax(string $id) {
        $supplier = SupplierModel::find($id);
        return view('supplier.edit_ajax', ['supplier' => $supplier]); // Adjust the view path as necessary
    }

    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_kode' => 'required|string|min:3|unique:m_supplier,supplier_kode,'.$id.',id',
                'supplier_nama' => 'required|string|max:100', 
                'supplier_alamat' => 'nullable|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil diubah'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data supplier tidak ditemukan'
                ]);
            }
        }
        return redirect('/supplier'); // Adjust the redirect path as necessary
    }

    public function confirm_ajax(string $id) {
        $supplier = SupplierModel::find($id);
        return view('supplier.confirm_ajax', ['supplier' => $supplier]); // Adjust the view path as necessary
    }

    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data supplier tidak ditemukan'
                ]);
            }
        }
        return redirect('/supplier'); // Adjust the redirect path as necessary
    }
}