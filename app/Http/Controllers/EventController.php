<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'categories' => 'required|string',
        ]);

        $categoriesData = [];
        $pairs = explode(',', $validated['categories']);

        foreach ($pairs as $pair) {
            if (str_contains($pair, ':')) {
                [$catName, $laps] = explode(':', $pair);
                $categoriesData[trim($catName)] = (int) trim($laps);
            } else {
                $categoriesData[trim($pair)] = 1; // Default 1 lap jika tidak diisi :lap
            }
        }

        $validated['categories'] = $categoriesData;
        $validated['status'] = 'upcoming';

        Event::create($validated);

        return back()->with('success', 'Event berhasil dibuat!');
    }

    // Fungsi untuk menghapus event
    public function destroy(Event $event)
    {
        $event->delete();

        return back()->with('success', 'Event beserta seluruh data atlet di dalamnya berhasil dihapus!');
    }

    public function resetRace(\App\Models\Event $event)
    {
        // Ambil semua ID atlet yang terdaftar di event ini
        $athleteIds = $event->athletes()->pluck('id');

        // Hapus secara permanen semua log waktu/lap untuk atlet-atlet tersebut
        \App\Models\RaceLog::whereIn('athlete_id', $athleteIds)->delete();

        return redirect()->back()->with('success', 'Data waktu dan lap berhasil di-reset. Data atlet dan RFID tetap tetap terisimpan.');
    }
}
