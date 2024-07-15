@extends('dashboard.layouts.main')

<!-- Include Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@section('container')
    <!-- Tabel Update -->
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light text-center rounded p-4">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0">Riwayat Monitoring</h4>
                <a class="btn btn-secondary" target="_blank" href="{{ route('dashboard.cetak', ['lokasi_id' => request('lokasi_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                    <i class="bx bx-printer"></i> Cetak
                </a>
            </div>
            <form action="{{ route('dashboard.filter') }}" method="GET">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <span class="input-group-text">Dari Tanggal</span>
                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Pilih Tanggal dan Waktu" value="{{ request('start_date') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <span class="input-group-text">Sampai Tanggal</span>
                            <input type="text" class="form-control" id="end_date" name="end_date" placeholder="Pilih Tanggal dan Waktu" value="{{ request('end_date') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <span class="input-group-text">Pilih Lokasi</span>
                            <select class="form-select" name="lokasi_id">
                                <option value="">Pilih Lokasi</option>
                                @foreach($lokasi_monitoring as $lokasiId => $lokasiName)
                                    <option value="{{ $lokasiId }}" {{ request('lokasi_id') == $lokasiId ? 'selected' : '' }}>{{ $lokasiName }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-warning" type="submit"><i class="bx bx-search"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </form>


            <div class="table-responsive">
                @if ($sensordata->isEmpty())
                    <p class="text-center">Belum ada data</p>
                @else
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Tanggal</th>
                                <th scope="col">Pukul</th>
                                <th scope="col">Voltage(VA)</th>
                                <th scope="col">Power(W)</th>
                                <th scope="col">Power Factor</th>
                                <th scope="col">Energy(kWh)</th>
                                <th scope="col">Current(A)</th>
                                <th scope="col">Biaya(Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sensordata as $data)
                                <tr>
                                    <td>{{ $data->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $data->created_at->format('H:i') }}</td>
                                    <td>{{ $data->voltage }}</td>
                                    <td>{{ $data->power }}</td>
                                    <td>{{ $data->power_factor }}</td>
                                    <td>{{ $data->energy }}</td>
                                    <td>{{ $data->current }}</td>
                                    <td>{{ $data->biaya }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Tampilkan link pagination -->
                    {{ $sensordata->links() }}
                @endif
            </div>
        </div>
    </div>
    <!-- Akhir Tabel Update -->
    <script>
        flatpickr('#start_date', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });
        flatpickr('#end_date', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });
    </script>
@endsection
