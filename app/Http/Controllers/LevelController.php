<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

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

        if ($request->level_id){
            $level->where('level_id', $request->level_id);
        }

        return DataTables::of($level)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                $btn  = '<button onclick="modalAction(\''.url('/level/' . $level->level_id . 
                '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . 
                '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                            $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . 
                '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> ';  

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
        return redirect('/level'); 
    }

    public function import(){
        return view('level.import');
    }

    public function import_ajax(Request $request){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_level' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_level');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);
    
            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert[] = [
                            'level_id' => $value['A'],
                            'level_kode' => $value['B'],
                            'level_nama' => $value['C'],
                        ];
                    }
                }
    
                if (count($insert) > 0) {
                    LevelModel::insertOrIgnore($insert);
                }
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil diimport!'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport!'
                ]);
            }
        }
    
        return redirect('/');
    }

    public function export_excel() {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Level');
        $sheet->setCellValue('C1', 'Nama level');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($level as $data) {
            $sheet->setCellValue('A' . $baris, $no);           
            $sheet->setCellValue('B' . $baris, $data->level_kode);
            $sheet->setCellValue('C' . $baris, $data->level_nama);
        
            $no++;
            $baris++;
        }
        

        foreach(range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'level_user_' . date('Y-m-d_H-i-s') . '.xlsx';

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
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')->get();
    
        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();
    
        return $pdf->stream('Data_level_' . date('Y-m-d_His') . '.pdf');
    }
}