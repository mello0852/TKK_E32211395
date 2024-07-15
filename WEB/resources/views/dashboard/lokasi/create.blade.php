@extends('dashboard.layouts.main')
@section('container')
<div class="container mt-4">
    <h1 class="mb-4">Tambah Lokasi</h1>
    <form action="{{ route('lokasi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
            <input type="text" class="form-control" id="name" name="nama_lokasi" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Lokasi</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"></textarea>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-warning">Simpan</button>
            <a href="{{ route('lokasi.index') }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection