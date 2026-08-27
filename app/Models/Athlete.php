<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'club_name',
        'bib_number',
        'category',
        'gender',
        'rfid_tag',
    ];

    // Atribut tambahan yang akan otomatis disisipkan saat data dikirim ke JSON/Frontend
    // (Tambahkan 'is_finished' di sini)
    protected $appends = ['laps', 'formatted_time', 'time_seconds', 'is_finished', 'start_time'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relasi ke tabel log waktu
    public function raceLogs()
    {
        return $this->hasMany(RaceLog::class)->orderBy('scanned_at', 'asc');
    }

    // Kalkulasi jumlah Lap (Total scan dikurang 1 sebagai Start)
    public function getLapsAttribute()
    {
        $count = $this->raceLogs->count();
        return $count > 0 ? $count - 1 : 0;
    }

    // Kalkulasi Waktu dalam Detik (Untuk logika ranking: Makin kecil waktu = Makin cepat)
    public function getTimeSecondsAttribute()
    {
        $logs = $this->raceLogs;
        if ($logs->count() < 2) return 0; // Jika baru start atau belum ada data, waktu = 0

        $start = $logs->first()->scanned_at;
        $last = $logs->last()->scanned_at;

        return $start->diffInSeconds($last);
    }

    // Kalkulasi Waktu Visual (Format H:i:s)
    public function getFormattedTimeAttribute()
    {
        $logs = $this->raceLogs;
        if ($logs->count() < 2) return '00:00:00';

        $start = $logs->first()->scanned_at;
        $last = $logs->last()->scanned_at;

        return $start->diff($last)->format('%H:%I:%S');
    }

    // CEK APAKAH ATLET SUDAH FINISH
    public function getIsFinishedAttribute()
    {
        return $this->raceLogs->where('reader_location', 'Finish')->isNotEmpty();
    }

    // Mengambil waktu pasti kapan atlet melakukan scan pertama (Start)
    public function getStartTimeAttribute()
    {
        $firstLog = $this->raceLogs->first();
        return $firstLog ? $firstLog->scanned_at->toIso8601String() : null;
    }
}
