<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use Illuminate\Http\Request;

class AthleteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'bib_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'club_name' => 'nullable|string|max:255', // Validasi klub (opsional)
            'category' => 'required|string',
            'gender' => 'required|string',
        ]);

        Athlete::create($validated);

        return back()->with('success', 'Data atlet berhasil ditambahkan! Silakan lakukan pairing nanti.');
    }

    // Fungsi baru untuk memproses pairing
    public function updateRfid(Request $request, Athlete $athlete)
    {
        $validated = $request->validate([
            'rfid_tag' => 'required|string|unique:athletes,rfid_tag',
        ]);

        $athlete->update(['rfid_tag' => $validated['rfid_tag']]);

        return back()->with('success', 'RFID Tag berhasil dipasangkan ke atlet ' . $athlete->name);
    }
}
