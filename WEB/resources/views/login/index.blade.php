@extends('layouts.main')

@section('container')
<div class="container-xxl position-relative bg-white d-flex p-0">
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Sign In Start -->
    <div class="container-fluid">
        <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                @if (session()->has('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('loginError'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('loginError') }}
                        </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <a href="index.html" class="">
                            <h3 class="text-warning"><i class="fa fa-hashtag me-2"></i>VoltTech</h3>
                        </a>
                        <h3>Login</h3>
                    </div>

                    <form class="mb-3" action="/login" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Enter your username" autofocus required value="{{ old('username') }}">
                            @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter your password" aria-describedby="password" required>
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-warning py-3 w-100 mb-4">Login</button>
                    </form>

                    <!-- Pertanyaan -->
                    <div class="text-center">
                        <p>Belum memiliki akun? <a href="/register">Daftar disini</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Sign In End -->
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('dashmin/lib/chart/chart.min.js') }}"></script>
<script src="{{ asset('dashmin/lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('dashmin/lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('dashmin/lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('dashmin/lib/tempusdominus/js/moment.min.js') }}"></script>
<script src="{{ asset('dashmin/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
<script src="{{ asset('dashmin/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('dashmin/js/main.js') }}"></script>

{{-- Service-worker PWA --}}
{{-- <script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register("{{ asset('serviceworker.js') }}");
  }
</script> --}}
@endsection
