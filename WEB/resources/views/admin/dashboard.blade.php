@extends('dashboard.layouts.main')

@section('container')
<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!isset($users) && !isset($user))
        <!-- Search Users Form -->
        <h1 class="mb-4">Admin Dashboard</h1>
        <div class="card mb-4">
            <div class="card-header">
                <h4>Search Users</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.searchUsers') }}" method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="query" id="query" class="form-control" placeholder="Masukan nama, username, dan email" value="{{ old('query', $query ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-warning w-100"><i class="fas fa-search"></i> Search </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(isset($users))
        <!-- Search Results for Users -->
        <div class="card p-4 mb-4">
            <h2 class="mb-4">Users List</h2>
        
            @if($users->count() == 0)
                <p class="alert alert-warning">No users found with the name, username, or email "{{ $query }}" in the database.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a href="{{ route('admin.listLokasi', $user->id) }}" class="btn btn-warning btn-sm">View Lokasi</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination links -->
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif
        
        </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Back
            </a>
    @endif

    @if(isset($user) && isset($lokasiMonitoring))
    <div class="card mb-4">
        <div class="card-header">
            <h4>Lokasi for {{ $user->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.searchLokasi', $user->id) }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="query" id="query" class="form-control" placeholder="Enter lokasi name" value="{{ old('query', $query ?? '') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-search"></i> Search</button>
                </div>
            </form>
        </div>
    </div>
    @if($lokasiMonitoring->isEmpty())
        <p class="alert alert-warning">location not found with the name "{{ $query ?? ''}}" for this user.</p>
    @else
        <div class="row">
            @foreach($lokasiMonitoring as $lokasi)
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Daftar Lokasi</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Lokasi ID</th>
                                                    <th class="text-center">Nama Lokasi</th>
                                                    <th class="text-center">Blynk Token</th>
                                                    <th class="text-center">Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">{{ $lokasi->id }}</td>
                                                    <td>{{ $lokasi->nama_lokasi }}</td>
                                                    <td>{{ $lokasi->blynk_token }}</td>
                                                    <td class="text-center">
                                                        <form action="{{ route('admin.updateBlynkToken', $lokasi->id) }}" method="POST">
                                                            @csrf
                                                            <div class="input-group">
                                                                <input type="text" name="blynk_token" class="form-control" value="{{ $lokasi->blynk_token }}" required>
                                                                <button type="submit" class="btn btn-warning">Update</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endif
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        margin-bottom: 20px;
    }
    .btn {
        display: flex;
        align-items: center;
    }
    .btn i {
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
@endpush
