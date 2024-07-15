<?php
namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\LokasiMonitoring;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query data from the SensorData model
        $sensordata = SensorData::query();

        // Retrieve and store the start_date and end_date query parameters
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Filter data based on the date range if start_date and end_date are provided
        if ($start_date && $end_date) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i', $start_date);
            $endDate = Carbon::createFromFormat('Y-m-d H:i', $end_date);
            $sensordata->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Retrieve and store the lokasi_id query parameter
        $lokasiId = $request->input('lokasi_id');

        // Filter data based on lokasi_id if it is provided
        if ($lokasiId) {
            $sensordata->where('lokasi_id', $lokasiId)->whereHas('lokasiMonitoring', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        } else {
            // Filter data only by lokasiMonitoring owned by the user
            $sensordata->whereHas('lokasiMonitoring', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        // Mendapatkan data lokasi dari model Lokasi yang dimiliki oleh user saat ini
        $lokasiOptions = LokasiMonitoring::where('user_id', $user->id)->pluck('nama_lokasi', 'id');

        // Pagination with the query parameters start_date and end_date
        $sensordata = $sensordata->latest()->paginate(15)->appends(['start_date' => $start_date, 'end_date' => $end_date, 'lokasi_id' => $lokasiId]);

        // Return data to the view, sorted from newest to oldest
        return view('dashboard.histories.index', [
            'title' => 'Dashboard | Histories',
            'today' => Carbon::now()->format('Y-m-d'),
            'sensordata' => $sensordata,
            'lokasi_monitoring' => $lokasiOptions,
        ]);
    }

    public function cetak(Request $request)
    {
        $user = Auth::user();
        $sensordata = SensorData::query();

        $lokasiName = $request->input('lokasi_name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if ($start_date && $end_date) {
            $startDate = Carbon::parse($start_date)->startOfDay();
            $endDate = Carbon::parse($end_date)->endOfDay();
            $sensordata->whereBetween('created_at', [$startDate, $endDate]);
        }

        $lokasiId = $request->input('lokasi_id');

        if ($lokasiId) {
            $lokasimonitoring = LokasiMonitoring::find($lokasiId);
            if ($lokasimonitoring) {
                $sensordata->where('lokasi_id', $lokasiId)->whereHas('lokasiMonitoring', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            } else {
                return redirect()->back()->withErrors('Lokasi tidak ditemukan.');
            }
        } else {
            $sensordata->whereHas('lokasiMonitoring', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
            $lokasimonitoring = null; // or any default value indicating all locations
        }

        return view('dashboard.histories.cetakhistory', [
            'title' => 'Dashboard | Cetak History',
            'sensorData' => $sensordata->latest()->get(),
            'lokasiMonitoring' => $lokasimonitoring,
            'lokasi_name' => $lokasiName ?? 'Semua Lokasi',
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    public function show($id)
    {
        $lokasimonitoring = LokasiMonitoring::findOrFail($id);
        $sensordata = SensorData::where('lokasi_id', $lokasimonitoring->id)->get();

        return view('data-monitoring', [
            'lokasiMonitoring' => $lokasimonitoring,
            'sensorData' => $sensordata
        ]);
    }

    public function destroy(SensorData $control)
    {
        $date = $control->created_at->format('Y-m-d');
        $control->delete();
        return redirect('/dashboard/sensordata?filter=' . $date)->with('success', 'Data berhasil dihapus!');
    }
}
