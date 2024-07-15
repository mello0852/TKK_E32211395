@extends('dashboard.layouts.main')

@section('container')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="mb-0">Add User</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Name</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter your name" autofocus required value="{{ old('name') }}">
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Username</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <input type="text" name="username" class="form-control" id="username" placeholder="Enter your username" required value="{{ old('username') }}">
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Email</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Password</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Confirm Password</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm your password" required>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-warning btn-block">Save</button>
                </div>
            </form>
        </div>
    </div>
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

@endsection
