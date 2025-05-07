<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

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

        if($request->kategori_id){
            $kategori->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {  
                $btn  = '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . 
                '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . 
                '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . 
                '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> '; 

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
        return view('kategori.create_ajax'); 
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

        return redirect('/kategori'); 
    }

    public function show_ajax(string $id) {
        $kategori = KategoriModel::find($id);
        return view('kategori.show_ajax', ['kategori' => $kategori]); 
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

    public function import() {
        return view('kategori.import');
    }

    public function import_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_kategori' =>  ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }

            $file = $request->file('file_kategori');
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
                            'kategori_kode' => $value['B'],
                            'kategori_nama' => $value['C'],
                        ];
                    }
                }
    
                if(count($insert) > 0){
                    KategoriModel::insertOrIgnore($insert);
                }

            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diimpor'
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
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Kategori');
        $sheet->setCellValue('C1', 'Nama Kategori');

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($kategori as $value) {
            $sheet->setCellValue('A'.$baris, $no);
            $sheet->setCellValue('B'.$baris, $value->kategori_kode);
            $sheet->setCellValue('C'.$baris, $value->kategori_nama);
            
            $baris++;
            $no++;

        }

        foreach(range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Kategori');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Kategori_'.date('Y-m-d_H-i-s').'.xlsx';

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
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')->get();

        $pdf = Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
        $pdf->setPaper('a4', 'potrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data_Kategori_'.date('Y-m-d_H:is').'.pdf');
    }
}
