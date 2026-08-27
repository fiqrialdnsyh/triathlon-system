<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\RaceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RaceLogController extends Controller
{
    // Endpoint yang dipanggil saat RFID terbaca
    public function store(Request $request)
    {
        $request->validate([
            'rfid_tag' => 'required|string',
            'reader_location' => 'required|string', // Contoh: 'Finish'
        ]);

        // Cari atlet berdasarkan rfid_tag hasil pairing
        $athlete = Athlete::where('rfid_tag', $request->rfid_tag)->first();

        if (!$athlete) {
            return response()->json(['message' => 'RFID Tag belum dipasangkan ke atlet manapun!'], 404);
        }

        // Simpan log waktu ke database
        RaceLog::create([
            'athlete_id' => $athlete->id,
            'reader_location' => $request->reader_location,
            'scanned_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Log waktu berhasil dicatat!',
            'athlete' => $athlete->name,
            'bib' => $athlete->bib_number
        ], 200);
    }
}
