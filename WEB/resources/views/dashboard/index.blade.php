@extends('dashboard.layouts.main')

@section('container')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/3.0.0/fetch.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<div class="container-fluid pt-4 px-4">
    <!-- Input untuk memilih token Blynk -->
    <!-- <div class="card mb-4">
        <div class="card-header">
            <h4>Lokasi</h4>
        </div>
            <div class="card-body"> -->
                <div class="input-group rounded-pill overflow-hidden mb-4">
                    <span class="input-group-text border-0 bg-lightgray">Pilih Lokasi</span>
                    <select class="form-select border-0 bg-light" name="blynkTokenInput" id="blynkTokenInput">
                    <option value="">Nama Lokasi</option>
                @foreach($lokasi_monitoring as $lokasi)
                    <option value="{{ $lokasi->blynk_token }}" data-nama_lokasi="{{ $lokasi->nama_lokasi }}" data-alamat="{{ $lokasi->alamat }}" data-deskripsi="{{ $lokasi->deskripsi }}" data-owner="{{ $lokasi->user->name }}">{{ $lokasi->nama_lokasi }}</option>
                @endforeach
                    </select>
                    <button id="btnPilih" class="btn btn-warning border-0"><i class="bx bx-search"></i> Cari</button>
                </div>
            <!-- </div> 
        </div> -->

    <!-- Location Information -->
    <div id="lokasiInfo" class="card mb-4">
        <div class="card-header">
            <h4>Informasi Lokasi</h4>
        </div>
        <div class="card-body rounded-bottom p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-industry me-3"></i>
                <p class="mb-0"><strong>Nama Lokasi:</strong> <span id="lokasiNama"></span></p>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-map-marker-alt me-3"></i>
                <p class="mb-0"><strong>Alamat:</strong> <span id="lokasiAlamat"></span></p>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-clipboard me-3"></i>
                <p class="mb-0"><strong>Deskripsi:</strong> <span id="lokasiDeskripsi"></span></p>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-user me-3"></i>
                <p class="mb-0"><strong>Pemilik Kebun:</strong> <span id="lokasiOwner"></span></p>
            </div>
        </div>
    </div>

    <!-- Monitoring RealTime -->
    <div class="card mb-4">
    <div class="card-header">
        <h4>Monitoring RealTime</h4>
    </div>
    <div class="card-body">
        <div class="row g-4">
            @php
                $monitoring = [
                    ['icon' => 'fa-bolt', 'label' => 'Voltage', 'id' => 'voltage', 'unit' => 'VA'],
                    ['icon' => 'fa-bolt', 'label' => 'Power', 'id' => 'power', 'unit' => 'W'],
                    ['icon' => 'fa-bolt', 'label' => 'Power Factor', 'id' => 'power_factor', 'unit' => ''],
                    ['icon' => 'fa-bolt', 'label' => 'Energy', 'id' => 'energy', 'unit' => 'kWh'],
                    ['icon' => 'fa-bolt', 'label' => 'Current', 'id' => 'current', 'unit' => 'A'],
                    ['icon' => 'fa-money-bill', 'label' => 'Perkiraan Biaya Permenit', 'id' => 'biaya',  'unit' => 'Rupiah'],
                ];
            @endphp
            @foreach ($monitoring as $item)
            <div class="col-sm-6 col-xl-3">
                <div class="bg-light rounded-pill d-flex align-items-center justify-content-between p-4">
                    <i class="fa-solid {{ $item['icon'] }} fa-2xl text-warning"></i>
                    <div class="ms-3">
                        <p class="mb-2">{{ $item['label'] }}</p>
                        <h6 class="mb-0"><span id="{{ $item['id'] }}"></span> {{ $item['unit'] }}</h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

    <!-- Monitoring Kontrol -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Monitoring Kontrol</h4>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @php
                    $controls = [
                        ['icon' => 'fa-plug', 'label' => 'Saklar', 'id' => 'otomatis'],
                    ];
                @endphp
                @foreach ($controls as $control)
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-light rounded-pill p-4">
                        <div class="form-group mb-0">
                            <i class="fa-solid {{ $control['icon'] }} fa-1xl text-warning"></i>
                            <label for="{{ $control['id'] }}">{{ $control['label'] }}</label>
                            <input type="checkbox" checked data-toggle="toggle" data-on="On" data-off="Off" data-onstyle="primary" data-offstyle="danger" id="{{ $control['id'] }}" onchange="updateSaklar()">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <h6 class="mb-4">Data Sensor Chart</h6>
        <form id="dataForm">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <select id="lokasiDropdown" name="lokasi_id" class="form-control" required>
                        <option value="" disabled selected>Pilih Lokasi</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <select id="range" name="range" class="form-control">
                        <option value="1jam">1 Jam</option>
                        <option value="1hari">1 Hari</option>
                        <option value="1minggu">1 Minggu</option>
                        <option value="1bulan">1 Bulan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-block">Search</button>
                </div>
            </div>
        </form>
        <canvas id="dataChart" class="mt-4"></canvas>
        <div class="mt-4">
                <h5>Total Biaya Per Hari: <span id="totalBiaya"></span> Rupiah</h5>
            </div>
    </div>
</div>


<!-- Table Update -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Riwayat Monitoring Terbaru</h6>
            <a href="/dashboard/controls" class="text-warning">Lihat Semua</a>
        </div>
        <div class="table-responsive">
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
                    @foreach($sensor_data as $data)
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
@include('dashboard.realtime')
<!-- Table Update End -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchLokasi();
    });

    function fetchLokasi() {
    fetch('/get-lokasi-by-user')
        .then(response => response.json())
        .then(data => {
            const lokasiDropdown = document.getElementById('lokasiDropdown');
            lokasiDropdown.innerHTML = ''; // Clear previous options
            // Add initial option
            const initialOption = document.createElement('option');
            initialOption.value = '';
            initialOption.disabled = true;
            initialOption.selected = true;
            initialOption.textContent = 'Pilih Lokasi';
            lokasiDropdown.appendChild(initialOption);
            // Add options for each lokasi
            data.forEach(lokasi => {
                const option = document.createElement('option');
                option.value = lokasi.id;
                option.textContent = lokasi.nama_lokasi;
                lokasiDropdown.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching lokasi data:', error));
}

    let chartInstance = null;

    document.getElementById('dataForm').addEventListener('submit', function(event) {
        event.preventDefault();
        fetchData();
    });

    function fetchData() {
        const lokasiId = document.getElementById('lokasiDropdown').value;
        const range = document.getElementById('range').value;

        fetch(`/get-data?lokasi_id=${lokasiId}&range=${range}`)
            .then(response => response.json())
            .then(data => {
                renderChart(data, range);
            })
            .catch(error => console.error('Error fetching data:', error));
    }
    
    function renderChart(data, range) {
        if (!data || data.length === 0) {
            alert('No data available');
            return;
        }

        const labels = data.map(item => range === '1jam' ? item.created_at : item.date);
        const voltage = data.map(item => item.avg_voltage);
        const power = data.map(item => item.avg_power);
        const powerFactor = data.map(item => item.avg_power_factor);
        const energy = data.map(item => item.avg_energy);
        const current = data.map(item => item.avg_current);
        const biaya = data.map(item => item.sum_biaya);

        const ctx = document.getElementById('dataChart').getContext('2d');

        // Clear previous chart instance
        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Voltage',
                        data: voltage,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Power',
                        data: power,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Power Factor',
                        data: powerFactor,
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Energy',
                        data: energy,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Current',
                        data: current,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Biaya',
                        data: biaya,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
           // Display total biaya only when range is '1bulan'
        if (range === '1bulan') {
            const totalBiaya = biaya.reduce((sum, currentBiaya) => sum + currentBiaya, 0);
            const formattedBiaya = Math.floor(totalBiaya).toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('totalBiaya').textContent = formattedBiaya;
            document.getElementById('totalBiayaContainer').style.display = 'block'; // Show total biaya container
        } else {
            document.getElementById('totalBiayaContainer').style.display = 'none'; // Hide total biaya container if range is not '1bulan'
        }
    
    }
</script>

<script>
    let previousToken = localStorage.getItem('previousToken');
    let pollingIntervalId = null;
    let currentToken = null;

    function updateLokasiInfo(token) {
        const selectedOption = $('#blynkTokenInput option:selected');
        $('#lokasiNama').text(selectedOption.data('nama_lokasi'));
        $('#lokasiAlamat').text(selectedOption.data('alamat'));
        $('#lokasiDeskripsi').text(selectedOption.data('deskripsi'));
        $('#lokasiOwner').text(selectedOption.data('owner'));
        $('#lokasiInfo').show();
    }

    document.getElementById('btnPilih').addEventListener('click', function() {
            var selectedToken = document.getElementById('blynkTokenInput').value;

            if (selectedToken === "") {
                resetBlynkDataToZero(); // Panggil reset jika token kosong
                selectedToken = "Data Buatan";
            }

            if (selectedToken !== previousToken) {
                console.log('Token Blynk yang baru dipilih:', selectedToken);
                if (previousToken) {
                    localStorage.removeItem(previousToken);
                }
                localStorage.setItem('previousToken', selectedToken);
                previousToken = selectedToken;
                currentToken = selectedToken;

                if (pollingIntervalId !== null) {
                    clearInterval(pollingIntervalId);
                    pollingIntervalId = null;
                }

                updateBlynkData(selectedToken);
            } else {
                console.log('Token Blynk yang dipilih sama dengan token sebelumnya:', selectedToken);
            }
            updateLokasiInfo(selectedToken);
            updateBlynkValues(selectedToken);
            if (previousToken) {
                updateBlynkData(previousToken);
            }
        });


    // Call the function to fetch daily cost when the page loads
    //document.addEventListener('DOMContentLoaded', fetchDailyCost);

    function resetBlynkDataToZero() {
        console.log('Resetting Blynk data to 0');
        document.getElementById('voltage').textContent = '0';
        document.getElementById('power').textContent = '0';
        document.getElementById('power_factor').textContent = '0';
        document.getElementById('energy').textContent = '0';
        document.getElementById('current').textContent = '0';
        document.getElementById('biaya').textContent = '0';
        $('#otomatis').bootstrapToggle('off');
    }

    function updateBlynkData(token) {
        currentToken = token;
        const pollingInterval = 1000;
        updateLokasiInfo(token);
        pollingIntervalId = setInterval(() => {
            fetchBlynkData('v2', token, 'voltage');
            fetchBlynkData('v3', token, 'current');
            fetchBlynkData('v4', token, 'power_factor');
            fetchBlynkData('v5', token, 'power');
            fetchBlynkData('v6', token, 'energy');
            fetchBlynkData('v7', token, 'biaya');
            fetchBlynkData('v1', token, 'otomatis', (value) => $('#otomatis').bootstrapToggle(value === '1' ? 'on' : 'off'));
        }, pollingInterval);
    }

    function fetchBlynkData(pin, token, elementId, transform = (value) => value) {
        if (token !== currentToken) {
            return;
        }

        const apiUrl = `https://blynk.cloud/external/api/get?token=${token}&pin=${pin}`;
        console.log(`Fetching data from ${apiUrl}`);
        fetch(apiUrl, { method: 'GET' })
            .then(response => {
                console.log(`Response status for pin ${pin}: ${response.status}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                console.log(`Data for pin ${pin}: ${data}`);
                if (token === currentToken) {
                    const transformedValue = transform(data);
                    document.getElementById(elementId).textContent = transformedValue;
                    console.log(`Updated element ${elementId} with value: ${transformedValue}`);
                }
            })
            .catch(error => {
                console.error(`Failed to fetch data for pin ${pin}:`, error);
            });
    }

    function updateBlynkValues(token) {
        getBlynkValue('v1', token);
        getBlynkValueString('v2', token);
        getBlynkValueString('v3', token);
        getBlynkValueString('v4', token);
        getBlynkValueString('v5', token);
        getBlynkValueString('v6', token);
        getBlynkValueString('v7', token);
    }

    $(document).ready(function() {
        
        // Reset page state on refresh
        $('#blynkTokenInput').val('');
        resetBlynkDataToZero();

        $('#blynkTokenInput').on('change', function() {
            const selectedToken = $(this).val();
            updateBlynkData(selectedToken);
        });

        $('#otomatis').change(function() {
            updateSaklar();
        });

    });

    function updateSaklar() {
        let token = localStorage.getItem('previousToken');
        if (!token) {
            console.error('Token Blynk tidak tersedia di local storage.');
            return;
        }

        let otomatisStatus = document.getElementById('otomatis').checked ? 1 : 0;
        updateBlynkPin(token, 'v1', otomatisStatus);
    }

    function updateBlynkPin(token, pin, value) {
        const apiUrl = `https://blynk.cloud/external/api/update?token=${token}&${pin}=${value}`;
        console.log(`Updating pin ${pin} to ${value} with URL ${apiUrl}`);
        fetch(apiUrl, { method: 'GET' })
            .then(response => {
                console.log(`Update response for pin ${pin}: ${response.status}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                console.log(`Pin ${pin} updated to ${value}`);
            })
            .catch(error => {
                console.error(`Failed to update pin ${pin}:`, error);
            });
    }

    function getBlynkValue(pin, token) {
        const apiUrl = `https://blynk.cloud/external/api/get?token=${token}&pin=${pin}`;
        console.log(`Getting value for pin ${pin} from URL ${apiUrl}`);
        fetch(apiUrl, { method: 'GET' })
            .then(response => {
                console.log(`Get value response for pin ${pin}: ${response.status}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                console.log(`Value from Blynk for pin ${pin}: ${data}`);
            })
            .catch(error => {
                console.error(`Failed to get value for pin ${pin}:`, error);
            });
    }

    function getBlynkValueString(pin, token) {
        const apiUrl = `https://blynk.cloud/external/api/get?token=${token}&pin=${pin}`;
        console.log(`Getting value for pin ${pin} from URL ${apiUrl}`);
        fetch(apiUrl, { method: 'GET' })
            .then(response => {
                console.log(`Get value response for pin ${pin}: ${response.status}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                console.log(`Value from Blynk for pin ${pin}: ${data}`);
            })
            .catch(error => {
                console.error(`Failed to get value for pin ${pin}:`, error);
            });
    }
</script>
@endsection
