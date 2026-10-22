<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        // Manajemen Users
        Route::resource('users', AdminController::class);
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/print', [\App\Http\Controllers\Admin\ReportController::class, 'printReport'])->name('reports.print');
    });

    // Front Desk Routes
    Route::prefix('frontdesk')->name('frontdesk.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'index'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'index'])->name('index');
        Route::post('/tickets/{ticket}/ambil', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'ambilTiket'])->name('tickets.ambil');
        Route::post('/tickets/{ticket}/distribusi', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'distribusi'])->name('tickets.distribusi');
        Route::post('/tickets/{ticket}/ubah-kategori', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'ubahKategori'])->name('tickets.ubah-kategori');
        Route::get('/ruang-kerja', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'ruangKerja'])->name('ruang-kerja');
        Route::post('/tickets/{ticket}/selesai', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'selesai'])->name('tickets.selesai');
        Route::get('/monitoring', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'monitoring'])->name('monitoring');
        Route::post('/tickets/{ticket}/tarik', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'tarikTiket'])->name('tickets.tarik');
        Route::get('/tickets/{ticket}/chat', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'chat'])->name('tickets.chat');
        Route::post('/tickets/{ticket}/chat', [\App\Http\Controllers\Frontdesk\FrontdeskController::class, 'kirimPesan'])->name('tickets.chat.send');
    });

    // Staf Bidang Routes
    Route::prefix('staf')->name('staf.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Staf\StafController::class, 'dashboard'])->name('dashboard');
        Route::post('/tickets/{ticket}/ambil', [\App\Http\Controllers\Staf\StafController::class, 'ambilTiket'])->name('tickets.ambil');
        Route::get('/tiket-saya', [\App\Http\Controllers\Staf\StafController::class, 'tiketSaya'])->name('tiket-saya');
        Route::post('/tickets/{ticket}/disposisi', [\App\Http\Controllers\Staf\StafController::class, 'disposisi'])->name('tickets.disposisi');
        Route::post('/tickets/{ticket}/selesai', [\App\Http\Controllers\Staf\StafController::class, 'selesai'])->name('tickets.selesai');
        Route::get('/tickets/{ticket}/chat', [\App\Http\Controllers\Staf\StafController::class, 'chat'])->name('tickets.chat');
        Route::post('/tickets/{ticket}/chat', [\App\Http\Controllers\Staf\StafController::class, 'kirimPesan'])->name('tickets.chat.send');
    });

    // Notifikasi Routes
    Route::post('/notifikasi/{notifikasi}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifikasi.read-all');
});
