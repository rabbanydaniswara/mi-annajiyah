<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PpdbController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FasilitasController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pendaftaran', [HomeController::class, 'pendaftaran'])->name('pendaftaran');
Route::get('/pendaftaran/cetak/{token}', [RegistrationController::class, 'printCard'])->name('pendaftaran.print');
Route::get('/tenaga-pendidik', [HomeController::class, 'tenagaPendidik'])->name('tenaga-pendidik');
Route::get('/fasilitas', [HomeController::class, 'fasilitas'])->name('fasilitas');
Route::get('/kegiatan', [HomeController::class, 'kegiatan'])->name('kegiatan');
Route::get('/cek-pendaftaran', [HomeController::class, 'cekPendaftaran'])->middleware('throttle:cek-pendaftaran')->name('cek-pendaftaran');
Route::post('/api/pendaftaran', [RegistrationController::class, 'store'])->middleware('throttle:pendaftaran')->name('api.pendaftaran');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:admin-login');

/*
|--------------------------------------------------------------------------
| Sitemap
|--------------------------------------------------------------------------
*/
Route::get('sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'priority' => '1.0'],
        ['loc' => route('pendaftaran'), 'priority' => '0.8'],
        ['loc' => route('tenaga-pendidik'), 'priority' => '0.8'],
        ['loc' => route('fasilitas'), 'priority' => '0.8'],
        ['loc' => route('kegiatan'), 'priority' => '0.8'],
        ['loc' => route('cek-pendaftaran'), 'priority' => '0.6'],
    ];
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $url) {
        $xml .= '<url><loc>' . e($url['loc']) . '</loc><priority>' . $url['priority'] . '</priority></url>';
    }
    $xml .= '</urlset>';
    return response($xml, 200)->header('Content-Type', 'text/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // PPDB
    Route::get('/ppdb', [PpdbController::class, 'index'])->name('ppdb');
    Route::post('/ppdb/update-status', [PpdbController::class, 'updateStatus'])->name('ppdb.updateStatus');
    Route::get('/ppdb/{siswa}/document/{field}', [PpdbController::class, 'document'])
        ->whereIn('field', ['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'])
        ->name('ppdb.document');
    Route::delete('/ppdb/{id}', [PpdbController::class, 'destroy'])->name('ppdb.destroy');

    // Konten
    Route::get('/konten', [KontenController::class, 'index'])->name('konten');
    Route::post('/konten/update', [KontenController::class, 'updateKonten'])->name('konten.update');
    Route::post('/konten/kegiatan', [KontenController::class, 'storeKegiatan'])->name('konten.storeKegiatan');
    Route::delete('/konten/kegiatan/{id}', [KontenController::class, 'destroyKegiatan'])->name('konten.destroyKegiatan');
    Route::post('/konten/kategori', [KontenController::class, 'storeKategori'])->name('konten.storeKategori');
    Route::delete('/konten/kategori/{id}', [KontenController::class, 'destroyKategori'])->name('konten.destroyKategori');
    Route::post('/konten/banner', [KontenController::class, 'storeBanner'])->name('konten.storeBanner');
    Route::put('/konten/banner', [KontenController::class, 'updateBanner'])->name('konten.updateBanner');
    Route::patch('/konten/banner/toggle/{id}', [KontenController::class, 'toggleBanner'])->name('konten.toggleBanner');
    Route::delete('/konten/banner/{id}', [KontenController::class, 'destroyBanner'])->name('konten.destroyBanner');

    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
    Route::get('/jadwal/print', [JadwalController::class, 'print'])->name('jadwal.print');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Guru
    Route::get('/guru', [GuruController::class, 'index'])->name('guru');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy');

    // Fasilitas
    Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas');
    Route::post('/fasilitas', [FasilitasController::class, 'store'])->name('fasilitas.store');
    Route::patch('/fasilitas/toggle/{id}', [FasilitasController::class, 'toggle'])->name('fasilitas.toggle');
    Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy'])->name('fasilitas.destroy');

    // Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    // Admin Management (admin role only; operator forbidden)
    Route::middleware('admin')->group(function () {
        Route::get('/admin-users', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin-users', [AdminController::class, 'store'])->name('admin.store');
        Route::delete('/admin-users/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    });

    // Export
    Route::get('/export/{type}', [ExportController::class, 'export'])->name('export');
});
