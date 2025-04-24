<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Penjualan',
            'list' => ['Home', 'penjualan']
        ];
        $page = (object) [
            'title' => 'Penjualan',
        ];
        $activeMenu = 'penjualan';

        $user = UserModel::all();

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 'page' => $page, 'user' => $user]);

    }

    public function list(Request $requet) {
        $penjualan = PenjualanModel::with(['user']);

        if($requet->user_id) {
            $penjualan->where('user_id', $requet->user_id);
        }
        return Datatables::of($penjualan)
        ->addIndexColumn()
        ->addColumn('user.username', function ($penjualan) {
            return $penjualan->user->username ?? '-';
        })
        ->addColumn('aksi', function ($penjualan) {
            $btn  = '<a href="'.url('/penjualan/' . $penjualan->user_id).'" class="btn btn-info btn-sm">Detail</a> ';
            $btn .= '<a href="'.url('/penjualan/' . $penjualan->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
            $btn .= '<form class="d-inline-block" method="POST" action="'.url('/penjualan/'.$penjualan->user_id).'">'
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

    public function create_ajax() {
        return view('penjualan.create_ajax'); // asumsi tidak butuh relasi lain, sesuaikan jika butuh data user atau produk
    }
    
    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|string|min:3|unique:t_penjualan,penjualan_kode',
                'pembeli' => 'required|string|max:100',
                'penjualan_tanggal' => 'required|date',
                'user_id' => 'required|integer' // jika user terkait
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }
    
            PenjualanModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan'
            ]);
        }
    
        return redirect('/penjualan');
    }
    
    public function edit_ajax(string $id) {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.edit_ajax', compact('penjualan'));
    }
    
    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|string|min:3|unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id',
                'pembeli' => 'required|string|max:100',
                'penjualan_tanggal' => 'required|date',
                'user_id' => 'required|integer'
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgfield' => $validator->errors(),
                ]);
            }
    
            $penjualan = PenjualanModel::find($id);
            if ($penjualan) {
                $penjualan->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diubah'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }
        }
    
        return redirect('/penjualan');
    }
    
    public function confirm_ajax(string $id) {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.confirm_ajax', compact('penjualan'));
    }
    
    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);
            if ($penjualan) {
                $penjualan->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }
        }
    
        return redirect('/penjualan');
    }
    
    public function show_ajax(string $id) {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.show_ajax', compact('penjualan'));
    }
    public function import() {
        return view('penjualan.import');
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
                        'penjualan_id' => $value['A'],
                        'user_id' => $value['B'],
                        'pembel' => $value['C'],
                        'penjualan_kode' => $value['D'],
                        'penjualan_tanggal' => $value['E'],
                        'created_at' => now(),
                    ];
                }
            }

            if(count($insert) > 0){
                PenjualanModel::insertOrIgnore($insert);
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
    $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
                ->orderBy('user_id')
                ->with('user')
                ->get();
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Kode Penjualan');
    $sheet->setCellValue('C1', 'Kode User');
    $sheet->setCellValue('D1', 'Pembeli');
    $sheet->setCellValue('E1', 'Kode Penjualan');
    $sheet->setCellValue('F1', 'Tanggal Penjualan');

    $sheet->getStyle('A1:F1')->getFont()->setBold(true);

    $no = 1;
    $baris = 2;

    foreach ($penjualan as $value) {
        $sheet->setCellValue('A'.$baris, $no);
        $sheet->setCellValue('B'.$baris, $value->penjualan_id);
        $sheet->setCellValue('C'.$baris, $value->user->user_id);
        $sheet->setCellValue('D'.$baris, $value->pembeli);
        $sheet->setCellValue('E'.$baris, $value->penjualan_kode);
        $sheet->setCellValue('F'.$baris, $value->penjualan_tanggal);

        $baris++;
        $no++;

    }

    foreach(range('A', 'F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $sheet->setTitle('Data Barang');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'Data_Penjualan_'.date('Y-m-d_H-i-s').'.xlsx';

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
    $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
                ->orderBy('user_id')
                ->with('user')
                ->get();

    $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
    $pdf->setPaper('a4', 'potrait');
    $pdf->setOption("isRemoteEnabled", true);
    $pdf->render();

    return $pdf->stream('Data_Penjualan_'.date('Y-m-d_H:is').'.pdf');
}
    
}
