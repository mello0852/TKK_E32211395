<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LokasiMonitoring;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        return view('admin.dashboard', ['title' => 'Admin']);
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', ['users' => $users, 'title' => 'Admin']);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('username', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->paginate(10);

        return view('admin.dashboard', [
            'users' => $users, 
            'query' => $query, 
            'title' => 'Admin'
        ]);
    }

    public function listLokasi($userId)
    {
        $user = User::findOrFail($userId);
        $lokasiMonitoring = LokasiMonitoring::where('user_id', $userId)->get();
    
        return view('admin.dashboard', [
            'user' => $user,
            'lokasiMonitoring' => $lokasiMonitoring,
            'title' => 'Admin'
        ]);
    }
    

    public function updateBlynkToken(Request $request, $lokasiId)
    {
        $lokasi = LokasiMonitoring::findOrFail($lokasiId);
        $lokasi->blynk_token = $request->input('blynk_token');
        $lokasi->save();

        return redirect()->back()->with('success', 'Blynk token updated successfully.');
    }

    public function searchLokasi(Request $request, $userId)
    {
        $query = $request->input('query');
        $user = User::findOrFail($userId);
        $lokasiMonitoring = LokasiMonitoring::where('user_id', $userId)
                ->where('nama_lokasi', 'LIKE', "%{$query}%")
                ->get();

        return view('admin.dashboard', [
            'user' => $user,
            'lokasiMonitoring' => $lokasiMonitoring,
            'query' => $query,
            'title' => 'Admin'
        ]);
    }

    public function create()
    {
        $title = 'Admin';
        return view('admin.users.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => ['required', 'alpha_dash', 'max:255', 'unique:users'],
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $title = 'Admin';
        return view('admin.users.edit', compact('user', 'title'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'alpha_dash', 'max:255', 'unique:users,username,' . $user->id],
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->status = $request->status ?? $user->status;

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->status = !$user->status;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully.');
    }
}
