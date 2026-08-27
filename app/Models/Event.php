<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'event_date',
        'categories',
        'status',
        'description',
    ];

    protected $casts = [
        'categories' => 'array',
    ];

    // Tambahkan fungsi relasi ini
    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }
}
