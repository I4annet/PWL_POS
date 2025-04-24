<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use Illuminate\Http\Request;
use App\Models\BarangModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SupplierModel;
use App\Models\UserModel;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Stok',
            'list' => ['Home', 'stok']
        ];

        $page = (object) [
            'title' => 'Stok Barang',
        ];

        $activeMenu = 'stok';

        $barang = BarangModel::all();

        $supplier = SupplierModel::all();

        $user = UserModel::all();

        return view('stok.index', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 'page' => $page, 'barang' => $barang, 
        'supplier' => $supplier, 'user' => $user]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::with(['barang', 'supplier', 'user']);

        if($request->barang_id) {
            $stoks->where('barang_id', $request->barang_id);
        }

        return Datatables::of($stoks)
        ->addIndexColumn()
        ->addColumn('barang.barang_nama', function ($stok) {
            return $stok->barang->barang_nama ?? '-';
        })
        ->addColumn('supplier.supplier_nama', function ($stok) {
            return $stok->supplier->supplier_nama ?? '-';
        })
        ->addColumn('user.username', function ($stok) {
            return $stok->user->username ?? '-';
        })
        ->addColumn('aksi', function ($stok) {
            $btn  = '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . 
            '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                        $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . 
            '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                        $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . 
            '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> ';  
            return $btn;
        })
        ->rawColumns(['aksi']) 
        ->make(true);
    }

    public function create_ajax() {
     
    $supplier = SupplierModel::all();
    $barang = BarangModel::all();

    // Return the view with the necessary data
    return view('stok.create_ajax', compact('supplier', 'barang'));
}

    public function store_ajax(Request $request) {
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'supplier_id'    => 'required|integer|exists:m_supplier,supplier_id',
            'barang_id'      => 'required|integer|exists:m_barang,barang_id',
            'stok_tanggal'   => 'required|date',
            'stok_jumlah'    => 'required|numeric|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgfield' => $validator->errors(),
            ]);
        }

        StokModel::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Data stok berhasil disimpan'
        ]);
    }

    return redirect('/stok');
}

    public function update_ajax(Request $request, string $id) {
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'supplier_id'    => 'required|integer|exists:m_supplier,supplier_id',
                    'barang_id'      => 'required|integer|exists:m_barang,barang_id',
                    'stok_tanggal'   => 'required|date',
                    'stok_jumlah'    => 'required|numeric|min:1',
                ];
        
                $validator = Validator::make($request->all(), $rules);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Validasi gagal',
                        'msgfield'  => $validator->errors(),
                    ]);
                }
        
                $stok = StokModel::find($id);
                if ($stok) {
                    $stok->update([
                        'supplier_id'   => $request->supplier_id,
                        'barang_id'     => $request->barang_id,
                        'stok_tanggal'  => $request->stok_tanggal,
                        'stok_jumlah'   => $request->stok_jumlah,
                        'user_id'       => auth()->id() // atau bisa disesuaikan kalau manual
                    ]);
        
                    return response()->json([
                        'status'    => true,
                        'message'   => 'Data stok berhasil diubah'
                    ]);
                } else {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Data stok tidak ditemukan'
                    ]);
                }
            }
        
            return redirect('/');
    }

    public function edit_ajax(string $id) {
        $barang = BarangModel::all();
        $supplier = SupplierModel::all(); 
    
        return view('stok.edit_ajax', compact('stok', 'barang', 'supplier'));
    }

    public function confirm_ajax(string $id) {
        $stok = StokModel::find($id);
        return view ('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function import(){
        return view('stok.import');
    }

    public function import_ajax(Request $request){
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        $file = $request->file('file_stok');
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
                        'barang_id'       => $value['A'],
                        'stok_tanggal'    => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['B']),
                        'stok_jumlah'     => $value['C'],
                        'stok_keterangan' => $value['D'] ?? null,
                    ];
                }
            }

            if (count($insert) > 0) {
                StokModel::insertOrIgnore($insert);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil diimport!'
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
        $stok = StokModel::with('barang')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Barang ID');
        $sheet->setCellValue('C1', 'Tanggal Stok');
        $sheet->setCellValue('D1', 'Jumlah Stok');
        $sheet->setCellValue('E1', 'Keterangan');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($stok as $data) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->barang_id);
            $sheet->setCellValue('C' . $baris, \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(new \DateTime($data->stok_tanggal)));
            $sheet->setCellValue('D' . $baris, $data->stok_jumlah);
            $sheet->setCellValue('E' . $baris, $data->stok_keterangan);
        
            $no++;
            $baris++;
        }
        

        foreach(range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'stok_barang_' . date('Y-m-d_H-i-s') . '.xlsx';

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
        $stok = StokModel::select('stok_id', 'barang_id', 'stok_tanggal', 'stok_jumlah', 'stok_keterangan')
            ->orderBy('barang_id')
            ->with('barang') // asumsi relasi kategori sudah didefinisikan di StokModel
            ->get();
    
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();
    
        return $pdf->stream('Data_Stok_' . date('Y-m-d_His') . '.pdf');
    }
    

}
