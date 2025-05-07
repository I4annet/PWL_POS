<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use App\Models\SupplierModel;
use App\Models\KategoriModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BarangController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];  
        $page = (object) [
            'title' => 'Daftar barang yang tersedia dalam sistem'
        ];

        $activeMenu = 'barang'; // Untuk indikator menu yang sedang aktif

        $kategori = KategoriModel::all(); // Ambil semua data kategori

        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Ambil data barang dalam bentuk JSON untuk DataTables
    public function list(Request $request) { 
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id', 'supplier_id')
                    ->with('kategori');

        if ($request->kategori_id) {
            $barang->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($barang)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {  
                $btn  = '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . 
                '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . 
                '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/user/' . $barang->barang_id . 
                '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> '; 

                return $btn; 
            })
            ->rawColumns(['aksi'])
            ->make(true); 
    }

    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];  
        $page = (object) [
            'title' => 'Tambah barang baru'
        ];

        $kategori = KategoriModel::all(); // Ambil semua data kategori

        $activeMenu = 'barang';

        return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request) {
        $request->validate([
            'barang_nama' => 'required|string|min:3',
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
            'kategori_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0'
        ]);

        BarangModel::create([
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'supplier_id' => $request->supplier_id,
            'barang_kode' => $request->barang_kode,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
    }

    public function show(string $id) {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail barang'
        ];

        $activeMenu = 'barang';

        return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id) {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit barang'
        ];

        $activeMenu = 'barang';

        $kategoris = KategoriModel::all(); // Ambil semua data kategori

        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu, 'kategoris' => $kategoris]);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'barang_nama' => 'required|string|min:3|unique:m_barang,barang_nama,'.$id.',barang_id',
            'kategori_id' => 'required|integer',
            'suplier_id' => 'required|integer',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0'
        ]);

        BarangModel::find($id)->update([
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'suplier_id' => $request->suplier_id,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    public function destroy(string $id) {
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            BarangModel::destroy($id);
            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/barang')->with('error', 'Data barang tidak bisa dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax() {
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get(); 
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')->get(); 
        
        return view('barang.create_ajax', compact('kategori', 'supplier')); 
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
                'barang_nama' => 'required|string|max:100', 
                'harga_beli' => 'required|numeric', 
                'harga_jual' => 'required|numeric', 
                'kategori_id' => 'required|integer', 
                'supplier_id' => 'required|integer' 
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            BarangModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan'
            ]);
        }

        return redirect('/barang'); // Adjust the redirect path as necessary
    }

    public function edit_ajax(string $id) {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')->get(); // Ambil semua data level

        return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori]);
    }

    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,'.$id.',barang_id',
                'barang_nama' => 'required|string|max:100', // nama maksimal 100 karakter
                'harga_beli' => 'required|numeric', // harga beli harus berupa angka
                'harga_jual' => 'required|numeric', // harga jual harus berupa angka
                'kategori_id' => 'required|integer', // kategori_id harus berupa angka
                'supplier_id' => 'required|integer' // supplier_id harus berupa angka
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data barang berhasil diubah'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data barang tidak ditemukan'
                ]);
            }
        }
        return redirect('/'); 
    }

    public function confirm_ajax(string $id) {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', ['barang' => $barang]); 
    }

    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data barang berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data barang tidak ditemukan'
                ]);
            }
        }
        return redirect('/barang'); // Adjust the redirect path as necessary
    }

    public function show_ajax(string $id) {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')->get();
        return view('barang.show_ajax', ['barang' => $barang, 'kategori' => $kategori]); 
    }

    public function import() {
        return view('barang.import'); 
    }

    public function import_ajax(Request $request) {
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_barang');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);
    
            $insert = [];
            if(count($data) > 1){
                foreach ($data as $baris => $value) {
                    if($baris > 1){
                        $insert[] = [
                            'kategori_id' => $value['A'],
                            'barang_kode' => $value['B'],
                            'barang_nama' => $value['C'],
                            'harga_beli' => $value['D'],
                            'harga_jual' => $value['E'],
                            'created_at' => now(),
                        ];
                    }
                }
    
                if(count($insert) > 0){
                    BarangModel::insertOrIgnore($insert);
                }
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel() {
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')
                    ->orderBy('kategori_id')
                    ->with('kategori')
                    ->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Harga Beli');
        $sheet->setCellValue('E1', 'Harga Jual');
        $sheet->setCellValue('F1', 'Kategori');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($barang as $value) {
            $sheet->setCellValue('A'.$baris, $no);
            $sheet->setCellValue('B'.$baris, $value->barang_kode);
            $sheet->setCellValue('C'.$baris, $value->barang_nama);
            $sheet->setCellValue('D'.$baris, $value->harga_beli);
            $sheet->setCellValue('E'.$baris, $value->harga_jual);
            $sheet->setCellValue('F'.$baris, $value->kategori->kategori_nama);

            $baris++;
            $no++;

        }

        foreach(range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Barang');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Barang_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        $writer->save('php://output');
        
        exit;
    }

    public function export_pdf() {
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')
                    ->orderBy('kategori_id')
                    ->with('kategori')
                    ->get();

        $pdf = Pdf::loadView('barang.export_pdf', ['barang' => $barang]);
        $pdf->setPaper('a4', 'potrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data_Barang_'.date('Y-m-d_H:is').'.pdf');
    }
    
    }
