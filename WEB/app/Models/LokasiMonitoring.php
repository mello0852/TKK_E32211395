<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiMonitoring extends Model
{
    use HasFactory;

    protected $table = 'lokasi_monitoring';

    protected $fillable = [
        'user_id',
        'nama_lokasi',
        'alamat',
        'blynk_token',
        'deskripsi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sensorData()
    {
        return $this->hasMany(SensorData::class, 'lokasi_id');
    }
}
