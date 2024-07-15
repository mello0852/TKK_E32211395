<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;

class DashboardController extends Controller
{
    public function index()
    {
        $SensorData = SensorData::latest()->paginate(5); // Mendapatkan semua data sensor tanah
        $title = "Dashboard | Histories"; // Anda juga bisa menetapkan nilai variabel $title di sini

        return view('dashboard.index', [
            'SensorData' => $SensorData, // Melewatkan variabel $dataSensorSoil ke tampilan
            'title' => $title, // Melewatkan variabel $title ke tampilan
        ]);
    }
}