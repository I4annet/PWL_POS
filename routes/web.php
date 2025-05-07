<?php

use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\PenjualanController;
use Monolog\Level;

// Route::get('/', [WelcomeController::class, 'index']); 

Route::pattern('id', '[0-9]+');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'store']);
Route::get('login', [AuthController::class, 'login']) -> name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

    Route::get('/', [WelcomeController::class, 'index']);

    Route::middleware(['auth'])->group(function () {
        Route::get('/', [WelcomeController::class, 'index']);

    Route::get('/profile', [ProfileController::class, 'index']); // Menampilkan halaman profile
    Route::get('/profile/import', [ProfileController::class, 'import']);
    Route::post('/profile/import_ajax', [ProfileController::class, 'import_ajax']); // Menampilkan pop up import profile

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);          // Menampilkan halaman awal user
        Route::post('/list', [UserController::class, 'list']);      // Menampilkan data user dalam bentuk json untuk datatables
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);  // Menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [UserController::class, 'store_ajax']);  // Menyimpan data user baru Ajax
        Route::get('/create', [UserController::class, 'create']);   // Menampilkan halamn form tambah user
        Route::post('/', [UserController::class, 'store']);         // Menyimpan data user baru  
        Route::get('/{id}', [UserController::class, 'show']);       // Menampilkan detail user
        Route::get('/{id}/edit', [UserController::class, 'edit']);  // Menampilkan halaman form edit user
        Route::put('/{id}', [UserController::class, 'update']);     // Menyimpan perubahan data user
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);  // Menampilkan halaman edit user Ajax
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);  // Menyimpan perubahan data user Ajax
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);  // Menampilkan konfirmasi hapus user Ajax
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);  // Menghapus data user
        Route::delete('/{id}', [UserController::class, 'destroy']);  // Menghapus data user
    });
    
    Route::group(['prefix' => 'level'], function () {
        Route::get('/', [LevelController::class, 'index']);          // Menampilkan halaman awal level
        Route::post('/list', [LevelController::class, 'list']);      // Menampilkan data level dalam bentuk json untuk datatables
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);  // Menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [LevelController::class, 'store_ajax']);  // Menyimpan data user baru Ajax
        Route::get('/create', [LevelController::class, 'create']);   // Menampilkan halamn form tambah level
        Route::post('/', [LevelController::class, 'store']);         // Menyimpan data level baru
        Route::get('/{id}', [LevelController::class, 'show']);       // Menampilkan detail level
        Route::get('/{id}/edit', [LevelController::class, 'edit']);  // Menampilkan halaman form edit level
        Route::put('/{id}', [LevelController::class, 'update']);     // Menyimpan perubahan data level
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);  // Menampilkan halaman edit user Ajax
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);  // Menyimpan perubahan data user Ajax
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);  // Menampilkan konfirmasi hapus user Ajax
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);  // Menghapus data user
        Route::delete('/{id}', [LevelController::class, 'destroy']);  // Menghapus data level
    });
    
    Route::group(['prefix' => 'kategori'], function () {
        Route::get('/', [KategoriController::class, 'index']);          // Menampilkan halaman awal kategori
        Route::post('/list', [KategoriController::class, 'list']);      // Menampilkan data kategori dalam bentuk json untuk datatables
        Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);  // Menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [KategoriController::class, 'store_ajax']);  // Menyimpan data user baru Ajax
        Route::get('/create', [KategoriController::class, 'create']);   // Menampilkan halamn form tambah kategori
        Route::post('/', [KategoriController::class, 'store']);         // Menyimpan data kategori baru
        Route::get('/{id}', [KategoriController::class, 'show']);       // Menampilkan detail kategori
        Route::get('/{id}/edit', [KategoriController::class, 'edit']);  // Menampilkan halaman form edit kategori
        Route::put('/{id}', [KategoriController::class, 'update']);     // Menyimpan perubahan data kategori
        Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);  // Menampilkan halaman edit user Ajax
        Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);  // Menyimpan perubahan data user Ajax
        Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);  // Menampilkan konfirmasi hapus user Ajax
        Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);  // Menghapus data user
        Route::delete('/{id}', [KategoriController::class, 'destroy']);  // Menghapus data kategori
    });
    
    Route::group(['prefix' => 'barang'], function () {
        Route::get('/', [BarangController::class, 'index']);          // Menampilkan halaman awal barang
        Route::post('/list', [BarangController::class, 'list']);      // Menampilkan data barang dalam bentuk json untuk datatables
        Route::get('/create_ajax', [BarangController::class, 'create_ajax']);  // Menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [BarangController::class, 'store_ajax']);  // Menyimpan data user baru Ajax
        Route::get('/create', [BarangController::class, 'create']);   // Menampilkan halamn form tambah barang
        Route::post('/', [BarangController::class, 'store']);         // Menyimpan data barang baru
        Route::get('/{id}', [BarangController::class, 'show']);       // Menampilkan detail barang
        Route::get('/{id}/edit', [BarangController::class, 'edit']);  // Menampilkan halaman form edit barang
        Route::put('/{id}', [BarangController::class, 'update']);     // Menyimpan perubahan data barang
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);  // Menampilkan halaman edit user Ajax
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);  // Menyimpan perubahan data user Ajax
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);  // Menampilkan konfirmasi hapus user Ajax
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);  // Menghapus data user
        Route::delete('/{id}', [BarangController::class, 'destroy']);  // Menghapus data barang
    });
    
    Route::group(['prefix' => 'supplier'], function () {
        Route::get('/', [SupplierController::class, 'index']);          // Menampilkan halaman awal supplier
        Route::post('/list', [SupplierController::class, 'list']);      // Menampilkan data supplier dalam bentuk json untuk datatables 
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);  // Menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [SupplierController::class, 'store_ajax']);  // Menyimpan data user baru Ajax
        Route::get('/create', [SupplierController::class, 'create']);   // Menampilkan halamn form tambah supplier
        Route::post('/', [SupplierController::class, 'store']);         // Menyimpan data supplier baru
        Route::get('/{id}', [SupplierController::class, 'show']);       // Menampilkan detail supplier
        Route::get('/{id}/edit', [SupplierController::class, 'edit']);  // Menampilkan halaman form edit supplier
        Route::put('/{id}', [SupplierController::class, 'update']);     // Menyimpan perubahan data supplier
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);  // Menampilkan halaman edit user Ajax
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);  // Menyimpan perubahan data user Ajax
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);  // Menampilkan konfirmasi hapus user Ajax
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);  // Menghapus data user
        Route::delete('/{id}', [SupplierController::class, 'destroy']);  // Menghapus data supplier

    });
        // Semua route di dalam group ini harus punya role ADM (Administrator) pada menu Level
        Route::middleware(['authorize:ADM'])->group(function () {
            Route::get('/level', [LevelController::class, 'index']);         
            Route::post('/level/list', [LevelController::class, 'list']);
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
            Route::post('/ajax', [LevelController::class, 'store_ajax']);
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']); 
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);  
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);  
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
            Route::get('/level/import', [LevelController::class, 'import']);
            Route::post('/level/import_ajax', [LevelController::class, 'import_ajax']);
            Route::get('/level/export_excel', [LevelController::class, 'export_excel']);
            Route::get('/level/export_pdf', [LevelController::class, 'export_pdf']);        
           
        });

        // Route di dalam group ini harus punya role ADM (Administrator) dan MNG (Manager) pada menu User
        Route::middleware(['authorize:ADM,MNG'])->group(function() {
            Route::get('/', [UserController::class, 'index']);   
            Route::post('/list', [UserController::class, 'list']);
            Route::get('/create_ajax', [UserController::class, 'create_ajax']);
            Route::post('/ajax', [UserController::class, 'store_ajax']);
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); 
            Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);  
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);  
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
            Route::get('/user/import', [UserController::class, 'import']);
            Route::post('/user/import_ajax', [UserController::class, 'import_ajax']);
            Route::get('/user/export_excel', [UserController::class, 'export_excel']);
            Route::get('/user/export_pdf', [UserController::class, 'export_pdf']);  
        });

         // Route di dalam group ini harus punya role ADM (Administrator) dan MNG (Manager) pada menu Supplier
         Route::middleware(['authorize:ADM,MNG'])->group(function() {
            Route::get('/', [SupplierController::class, 'index']);   
            Route::post('/list', [SupplierController::class, 'list']);
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);
            Route::post('/supplier/ajax', [SupplierController::class, 'store_ajax']);
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']); 
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);  
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);  
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
            Route::get('/supplier/import', [SupplierController::class, 'import']);
            Route::post('/supplier/import_ajax', [SupplierController::class, 'import_ajax']);
            Route::get('/supplier/export_excel', [SupplierController::class, 'export_excel']);
            Route::get('/supplier/export_pdf', [SupplierController::class, 'export_pdf']);   
        });

         // Route di dalam group ini harus punya role ADM (Administrator), MNG (Manager), dan STF (Staff) pada menu Kategori 
         Route::middleware(['authorize:ADM,MNG,STF'])->group(function() {
            Route::get('/', [KategoriController::class, 'index']);   
            Route::post('/list', [KategoriController::class, 'list']);
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);
            Route::post('/ajax', [KategoriController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']);
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']); 
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);  
            Route::get('/{id}/delete_ajax', [KategoriController::class, ' confirm_ajax']);  
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
            Route::get('/kategori/import', [KategoriController::class, 'import']);
            Route::post('/kategori/import_ajax', [KategoriController::class, 'import_ajax']);
            Route::get('/kategori/export_excel', [KategoriController::class, 'export_excel']);
            Route::get('/kategori/export_pdf', [KategoriController::class, 'export_pdf']);  
        });

         // Route di dalam group ini harus punya role ADM (Administrator), MNG (Manager), dan STF (Staff) pada menu Barang
         Route::middleware(['authorize:ADM,MNG,STF'])->group(function() {
            Route::get('/', [BarangController::class, 'index']);   
            Route::post('/list', [BarangController::class, 'list']);
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
            Route::post('/stok/ajax', [BarangController::class, 'store_ajax']);
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); 
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);  
            Route::get('/{id}/delete_ajax', [BarangController::class, ' confirm_ajax']);  
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
            Route::get('/barang/import', [BarangController::class, 'import']);
            Route::post('/barang/import_ajax', [BarangController::class, 'import_ajax']);
            Route::get('/barang/export_excel', [BarangController::class, 'export_excel']);
            Route::get('/barang/export_pdf', [BarangController::class, 'export_pdf']);  
        });

        Route::middleware('authorize:ADM,MNG')->group(function() {
            Route::get('/stok', [StokController::class, 'index']);
            Route::post('/stok/list', [StokController::class, 'list']);
            Route::get('/stok/create_ajax', [StokController::class, 'create_ajax']);
            Route::post('/stok/ajax', [StokController::class, 'store_ajax']);
            Route::get('/stok/{id}/edit_ajax', [StokController::class, 'edit_ajax']); 
            Route::put('/stok/{id}/update_ajax', [StokController::class, 'update_ajax']);
            Route::get('/stok/{id}/delete   _ajax', [StokController::class, 'confirm_ajax']);
            Route::delete('/stok/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
            Route::get('/stok/import', [StokController::class, 'import']);
            Route::post('/stok/import_ajax', [StokController::class, 'import_ajax']);
            Route::get('/stok/export_excel', [StokController::class, 'export_excel']);
            Route::get('/stok/export_pdf', [StokController::class, 'export_pdf']); 

        });

        Route::middleware('authorize:ADM, STF')->group(function(){
            Route::get('/penjualan', [PenjualanController::class, 'index']);
            Route::post('/penjualan/list', [PenjualanController::class, 'list']);
            Route::get('/penjualan/create_ajax', [PenjualanController::class, 'create_ajax']);
            Route::post('/penjualan/ajax', [PenjualanController::class, 'store_ajax']);
            Route::get('/penjualan/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax']);
            Route::get('/penjualan/{id}/show_ajax', [PenjualanController::class, 'show_ajax']);
            Route::put('/penjualan/{id}/update_ajax', [PenjualanController::class, 'update_ajax']);
            Route::get('/penjualan/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']);
            Route::delete('/penjualan/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']);
            Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy']);
            Route::get('/penjualan/import', [PenjualanController::class, 'import']);
            Route::post('/penjualan/import_ajax', [PenjualanController::class, 'import_ajax']);
            Route::get('/penjualan/export_excel', [PenjualanController::class, 'export_excel']);
            Route::get('/penjualan/export_pdf', [PenjualanController::class, 'export_pdf']); 

        });

        Route::middleware('authorize:ADM,MNG,STF')->group(function() {
            Route::get('/', [WelcomeController::class, 'index']);
         
        });

    });