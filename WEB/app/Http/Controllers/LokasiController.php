<?php

namespace App\Http\Controllers;

use App\Models\LokasiMonitoring;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LokasiController extends Controller
{
    /**
     * Display a listing of the user's lokasi_monitoring.
     */
    public function index()
    {
        $user = Auth::user();
        $lokasi_monitoring = LokasiMonitoring::where('user_id', $user->id)->get();
        $title = "Dashboard | Daftar Lokasi"; // Anda juga bisa menetapkan nilai variabel $title di sini
        return view('dashboard.lokasi.index', compact('lokasi_monitoring','title'));
    }

    /**
     * Show the form for creating a new lokasi.
     */
    public function create()
    {
        $users = User::all();
        $title = "Dashboard | Tambah Lokasi"; // Anda juga bisa menetapkan nilai variabel $title di sini
        return view('dashboard.lokasi.create', compact('users', 'title'));
    }

    /**
     * Store a newly created kebun in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
        ]);

        LokasiMonitoring::create([
            'nama_lokasi' => $request->nama_lokasi,
            'alamat' => $request->alamat,
            'deskripsi' => $request->deskripsi,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan');
    }

    /**
     * Display the specified lokasi.
     */
    public function show(LokasiMonitoring $lokasi)
    {
        $this->authorize('view', $lokasi);
        
        return view('lokasi.show', compact('lokasi'));
    }

    /**
     * Show the form for editing the specified lokasi.
     */
    public function edit(LokasiMonitoring $lokasi)
    {
        $this->authorize('update', $lokasi);
        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, LokasiMonitoring $lokasi)
    {
        $this->authorize('update', $lokasi);

        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
        ]);

        $lokasi->update($request->all());

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui');
    }

    public function destroy(LokasiMonitoring $lokasi)
    {
        $this->authorize('delete', $lokasi);
        $lokasi->delete();

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus');
    }

    public function getLokasiByUser(Request $request)
    {
        $user = $request->user();
        $lokasi = LokasiMonitoring::where('user_id', $user->id)->get(['id', 'nama_lokasi']); // Sesuaikan dengan nama kolom tabel Anda
        return response()->json($lokasi);
    }
}