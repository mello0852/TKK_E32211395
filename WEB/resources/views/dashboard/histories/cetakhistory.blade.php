<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title, and stylesheet links -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Monitoring | {{ $lokasiMonitoring->nama_lokasi ?? 'Semua Lokasi' }} - {{ request('filter') ?? (request('start_date') ? request('start_date').' hingga '.request('end_date') : now()->format('Y-m-d')) }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        /* Custom styles */
        body {
            font-family: 'Heebo', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container-xxl {
            padding-top: 50px;
        }
        .bg-light {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table th,
        .table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
        }
        @media print {
            body {
                background-color: #fff;
                color: #000;
            }
            .container-xxl {
                padding-top: 0;
            }
            .bg-light {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container-xxl position-relative">
        <!-- Content Start -->
        <div class="content">
            <div class="container-fluid">
                <div class="bg-light">
                    <div class="mb-4 text-center">
                        <h2 class="mb-3">Rekap Data Monitoring</h2>
                        <h4 class="mb-3">Lokasi: {{ $lokasiMonitoring->nama_lokasi ?? 'Semua Lokasi' }}</h4>
                        <h6 class="mb-0">
                            @if(request('filter'))
                                dari {{ request('filter') }}
                            @elseif(request('start_date') && request('end_date'))
                                dari {{ request('start_date') }} hingga {{ request('end_date') }}
                            @else
                                dari {{ now()->format('Y-m-d') }}
                            @endif
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Pukul</th>
                                    <th scope="col">Voltage</th>
                                    <th scope="col">Power</th>
                                    <th scope="col">Power Factor</th>
                                    <th scope="col">Energy</th>
                                    <th scope="col">Current</th>
                                    <th scope="col">Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sensorData as $data)
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
                    </div>
                </div>
            </div>
        </div>
        <!-- Content End -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Print Command -->
    <script>
        window.print();
    </script>
</body>
</html>
