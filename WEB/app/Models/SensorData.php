<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;
    
    protected $table = 'sensor_data'; // sesuaikan dengan nama tabel yang digunakan
    protected $primaryKey = 'id';
    protected $fillable = [
        'lokasi_id',
        'voltage',
        'power',
        'power_factor',
        'energy',
        'current',
        'biaya',
    ];
    
    public function lokasiMonitoring()
    {
        return $this->belongsTo(LokasiMonitoring::class, 'lokasi_id');
    }
}