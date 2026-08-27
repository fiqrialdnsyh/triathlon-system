<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Event;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AthleteController;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    $events = Event::orderBy('event_date', 'desc')->get();
    return view('welcome', compact('events'));
});

// Rute Halaman Leaderboard Live
Route::get('/event/{event}/leaderboard', function (Event $event) {
    // Tambahkan 'athletes.raceLogs' agar riwayat waktu langsung dimuat ke memori
    $event->load('athletes.raceLogs');
    return view('leaderboard', compact('event'));
})->name('leaderboard');

Route::get('/dashboard', function () {
    // Tambahkan with('athletes') agar data atlet per event ikut terbawa
    $events = Event::with('athletes')->orderBy('event_date', 'desc')->get();
    return view('dashboard', compact('events'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Tambahkan rute untuk memproses form (hanya bisa diakses jika sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    // INI YANG DITAMBAHKAN: Rute untuk menghapus event
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::post('/athletes', [AthleteController::class, 'store'])->name('athletes.store');
    Route::put('/athletes/{athlete}/rfid', [AthleteController::class, 'updateRfid'])->name('athletes.update_rfid');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// RUTE API UNTUK FITUR REAL-TIME (ALPINE.JS)
// ==========================================

// Route mengambil data atlet beserta kalkulasi waktu untuk Leaderboard
Route::get('/live-data/event/{event}/athletes', function (Event $event) {
    // Akan otomatis memuat atribut 'laps', 'formatted_time', dll dari model Athlete
    return response()->json($event->athletes);
});

// Route untuk mengambil tag baru yang belum terdaftar (Pairing Otomatis)
Route::get('/live-data/latest-tag', function () {
    return response()->json(['tag' => Cache::get('latest_unpaired_tag')]);
});

// Route untuk menghapus cache tag setelah berhasil masuk ke input
Route::post('/live-data/clear-tag', function () {
    Cache::forget('latest_unpaired_tag');
    return response()->json(['success' => true]);
});

Route::post('/events/{event}/reset-race', [App\Http\Controllers\EventController::class, 'resetRace'])->name('events.reset');

require __DIR__.'/auth.php';
