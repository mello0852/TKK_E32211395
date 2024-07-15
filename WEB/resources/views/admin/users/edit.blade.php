@extends('dashboard.layouts.main')

@section('container')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="mb-0">Edit Profile User</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Name</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Username</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Email</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Password <small>(leave blank to keep current password)</small></h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <h6 class="text-muted">Confirm Password</h6>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ $user->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$user->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-warning btn-block">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
