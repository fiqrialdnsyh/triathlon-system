<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'reader_location',
        'scanned_at',
    ];

    // Beritahu Laravel agar mengubah teks tanggal dari database menjadi objek Carbon (Waktu)
    protected $casts = [
        'scanned_at' => 'datetime',
    ];
}
