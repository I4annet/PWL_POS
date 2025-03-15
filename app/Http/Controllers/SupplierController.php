<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel; // Import LevelModel if needed

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

    // Ambil data supplier dalam bentuk JSON untuk DataTables 
    public function list(Request $request) { 
        $supplier = SupplierModel::select('id', 'supplier_kode', 'nama_supplier');

        return DataTables::of($supplier)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {  
                $btn  = '<a href="'.url('/supplier/' . $supplier->id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/supplier/' . $supplier->id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/supplier/'.$supplier->id).'">'
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
            'nama_supplier' => 'required|string|max:100', // nama maksimal 100 karakter
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
}