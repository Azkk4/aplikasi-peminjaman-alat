<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'petugas' => redirect()->route('petugas.peminjaman.index'),
        'peminjam' => redirect()->route('peminjam.dashboard'),
        default => abort(403),
    };
});

// ==========
// Admin
// ==========
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // CRUD Alat
        Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
        Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');

        // CRUD User
        Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');
        
        // CRUD Kategori
        Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('kategori.index');
        Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('kategori.create');
        Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
        Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('kategori.edit');
        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
        Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

        // CRUD Alat
        Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
        Route::get('/alat/create', [AdminController::class, 'createAlat'])->name('alat.create');
        Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');
        Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])->name('alat.edit');
        Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])->name('alat.update');
        Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])->name('alat.destroy');

        // CRUD Peminjaman
        Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])->name('peminjaman.create');
        Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])->name('peminjaman.store');
        Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])->name('peminjaman.updateStatus');
        Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])->name('peminjaman.destroy');

        // Kelola Pengembalian (Monitoring & History Pengembalian Alat)
        Route::get('/pengembalian', [AdminController::class, 'indexPengembalian'])->name('pengembalian.index');
    });


// ==========
// Petugas
// ==========
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {

    // Peminjaman & Persetujuan
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');
    Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])->name('peminjaman.tolak');

    // Pengembalian & Denda
    Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');

    // Cetak Laporan (Tambahkan method ini atau arahkan sementara)
    Route::get('/laporan', [PetugasController::class, 'indexLaporan'])->name('laporan.index');
});

// ==========
// Peminjam
// ==========
Route::middleware(['auth', 'role:peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    Route::get('/dashboard', [PeminjamController::class, 'dashboard'])->name('dashboard');
    Route::get('/katalog', [PeminjamController::class, 'katalogAlat'])->name('katalog.index');
    Route::get('/katalog/{alat}', [PeminjamController::class, 'detailAlat'])->name('katalog.show');
    Route::get('/peminjaman/create', [PeminjamController::class, 'createPeminjaman'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjaman.store');
    Route::get('/peminjaman', [PeminjamController::class, 'peminjamanSaya'])->name('peminjaman.index');
    Route::get('/peminjaman/{peminjaman}', [PeminjamController::class, 'detailPeminjaman'])->name('peminjaman.show');
    Route::get('/riwayat', [PeminjamController::class, 'riwayatPeminjaman'])->name('riwayat');
    Route::get('/profil', [PeminjamController::class, 'profil'])->name('profil');
    Route::put('/profil', [PeminjamController::class, 'updateProfil'])->name('profil.update');
});

// Route Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');