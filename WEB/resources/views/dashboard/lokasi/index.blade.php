@extends('dashboard.layouts.main')
@section('container')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-4">Daftar Lokasi</h1>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <a href="{{ route('lokasi.create') }}" class="btn btn-warning">Tambah Lokasi</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Nama Lokasi</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasi_monitoring as $lokasi)
                    <tr>
                        <td>{{ $lokasi->nama_lokasi }}</td>
                        <td>{{ $lokasi->alamat }}</td>
                        <td>{{ $lokasi->deskripsi }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info btn-fixed-width" data-bs-toggle="modal" data-bs-target="#detailModal{{ $lokasi->id }}">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="#" class="btn btn-sm btn-primary btn-fixed-width" data-bs-toggle="modal" data-bs-target="#editModal{{ $lokasi->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="#" class="btn btn-sm btn-danger btn-fixed-width" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $lokasi->id }}">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    table th, table td {
        text-align: left; /* Align text to the left */
    }
    .btn-fixed-width {
        width: 80px; /* Set a fixed width for the buttons */
        margin: 2px; /* Add some margin for spacing */
    }
</style>

<!-- Detail Modals -->
@foreach($lokasi_monitoring as $lokasi)
<div class="modal fade" id="detailModal{{ $lokasi->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $lokasi->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $lokasi->id }}">Detail Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Nama Lokasi: {{ $lokasi->nama_lokasi }} <br>
                Alamat: {{ $lokasi->alamat }} <br>
                Deskripsi: {{ $lokasi->deskripsi }} <br>
                Pemilik Lokasi: {{ $lokasi->user->name }} <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endforeach

<!-- Edit Modals -->
@foreach($lokasi_monitoring as $lokasi)
<div class="modal fade" id="editModal{{ $lokasi->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $lokasi->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $lokasi->id }}">Edit Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lokasi.update', $lokasi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" value="{{ $lokasi->nama_lokasi }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $lokasi->alamat }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ $lokasi->deskripsi }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Delete Modals -->
@foreach($lokasi_monitoring as $lokasi)
<div class="modal fade" id="deleteModal{{ $lokasi->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $lokasi->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $lokasi->id }}">Hapus Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus lokasi ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <form action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteForms = document.querySelectorAll('form.d-inline');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var confirmation = confirm('Apakah Anda yakin ingin menghapus lokasi ini?');
            if (!confirmation) {
                event.preventDefault();
            }
        });
    });
});
</script>