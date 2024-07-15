<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\LokasiMonitoring;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SensorDataController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $lokasi_monitoring = LokasiMonitoring::where('user_id', $user->id)->get();
        $sensor_data = SensorData::whereIn('lokasi_id', $lokasi_monitoring->pluck('id'))
                                ->orderBy('created_at', 'desc') // Order by creation date
                                ->take(5) // Take the latest 5 records
                                ->get();
        $title = "Dashboard | Daftar Lokasi"; // Anda juga bisa menetapkan nilai variabel $title di sini
        return view('dashboard.index', compact('lokasi_monitoring','title', 'sensor_data'));
    }
    
    public function bacavoltage()
    {
        $sensorlatestData = SensorData::latest()->first();
        return response()->json($sensorlatestData->voltage);
    }

    public function bacapower()
    {
        $sensorlatestData = SensorData::latest()->first();
        return response()->json($sensorlatestData->power);
    }

    public function bacapowerfactor()
    {
        $sensorlatestData = SensorData::latest()->first();
        return response()->json($sensorlatestData->power_factor);
    }

    public function bacaenergy()
    {
        $sensorlatestData = SensorData::latest()->first();
        return response()->json($sensorlatestData->energy);
    }

    public function bacacurrent()
    {
        $sensorlatestData = SensorData::latest()->first();
        return response()->json($sensorlatestData->current);
    }

    public function bacabiaya()
    {
        $sensorlatestData = SensorData::latest()->first();
        return response()->json($sensorlatestData->biaya);
    }

    public function updateSwitch1(Request $request) {
        // Tangani logika untuk memperbarui status switch 1
        $status = $request->input('status');
        // Simpan status switch 1
        return response()->json(['success' => true]);
    }
    
    public function getBlynkToken(Request $request)
    {
        // Ambil nama lokasi dari request
        $namaLokasi = $request->input('lokasi');
        
        // Cari lokasi berdasarkan nama
        $lokasi = LokasiMonitoring::where('name', $namaLokasi)->first();
        
        // Jika lokasi ditemukan, kembalikan token Blynk
        if ($lokasi) {
            return response()->json(['blynk_token' => $lokasi->blynk_token]);
        }
        
        // Jika lokasi tidak ditemukan, kembalikan pesan kesalahan
        return response()->json(['error' => 'Lokasi tidak ditemukan'], 404);
    }
    
    public function updateBlynkToken(Request $request)
    {
        // Ambil token baru dari permintaan
        $newToken = $request->input('token');
        
        // Ambil nama lokasi yang sedang dipilih oleh pengguna
        $selectedLokasi = $request->input('lokasi');
        
        // Cari lokasi berdasarkan nama
        $lokasi = LokasiMonitoring::where('nama_lokasi', $selectedLokasi)->first();
        
        // Jika lokasi ditemukan, perbarui token Blynk-nya
        if ($lokasi) {
            $lokasi->blynk_token = $newToken;
            $lokasi->save(); // Simpan perubahan
            
            // Beri respons bahwa token telah diperbarui
            return response()->json(['message' => 'Token Blynk berhasil diperbarui']);
        }
        
        // Jika lokasi tidak ditemukan, kembalikan pesan kesalahan
        return response()->json(['error' => 'Lokasi tidak ditemukan'], 404);
    }

    public function getLokasiInfo(Request $request)
    {
        $token = $request->input('token');
        $lokasi = LokasiMonitoring::where('blynk_token', $token)->first();
    
        if ($lokasi) {
            $user = $lokasi->user;
            return response()->json([
                'nama_lokasi' => $lokasi->nama_lokasi,
                'alamat' => $lokasi->alamat,
                'deskripsi' => $lokasi->deskripsi,
                'users' => $user->name,
            ]);
        } else {
            return response()->json(['error' => 'Lokasi not found'], 404);
        }
    }

    public function getData(Request $request) {
        $lokasiId = $request->input('lokasi_id');
        $range = $request->input('range');
    
        $query = \DB::table('sensor_data')->where('lokasi_id', $lokasiId);
    
        if ($range == '1jam') {
            $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as created_at, AVG(voltage) as avg_voltage, AVG(power) as avg_power, AVG(power_factor) as avg_power_factor, AVG(energy) as avg_energy, AVG(current) as avg_current, SUM(biaya) as sum_biaya')
                          ->where('created_at', '>=', \Carbon\Carbon::now()->subHour())
                          ->groupBy('created_at')
                          ->orderBy('created_at', 'desc')
                          ->take(60)
                          ->get();
        } elseif ($range == '1hari') {
            $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H") as date, AVG(voltage) as avg_voltage, AVG(power) as avg_power, AVG(power_factor) as avg_power_factor, AVG(energy) as avg_energy, AVG(current) as avg_current, SUM(biaya) as sum_biaya')
                          ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
                          ->groupBy('date')
                          ->orderBy('date', 'desc')
                          ->take(24)
                          ->get();
        } elseif ($range == '1minggu') {
            $data = $query->selectRaw('DATE(created_at) as date, AVG(voltage) as avg_voltage, AVG(power) as avg_power, AVG(power_factor) as avg_power_factor, AVG(energy) as avg_energy, AVG(current) as avg_current, SUM(biaya) as sum_biaya')
                          ->where('created_at', '>=', \Carbon\Carbon::now()->subWeek())
                          ->groupBy('date')
                          ->orderBy('date', 'desc')
                          ->take(7)
                          ->get();
        } elseif ($range == '1bulan') {
            $data = $query->selectRaw('DATE(created_at) as date, AVG(voltage) as avg_voltage, AVG(power) as avg_power, AVG(power_factor) as avg_power_factor, AVG(energy) as avg_energy, AVG(current) as avg_current, SUM(biaya) as sum_biaya')
                          ->where('created_at', '>=', \Carbon\Carbon::now()->subMonth())
                          ->groupBy('date')
                          ->orderBy('date', 'desc')
                          ->take(30)
                          ->get();
        } else {
            return response()->json(['error' => 'Invalid range specified'], 400);
        }
    
        return response()->json($data);
    }
    
    public function getWeeklyEnergyCost(Request $request)
    {
    $lokasiId = $request->input('lokasi_id');
    $tarif = 1352; // Tarif listrik per unit energi

    $data = \DB::table('sensor_data')
                ->selectRaw('DATE(created_at) as date, SUM(energy) as sum_energy, (SUM(energy) * ?) as total_biaya', [$tarif])
                ->where('lokasi_id', $lokasiId)
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(7)
                ->get();

    return response()->json($data);
    }

    
}