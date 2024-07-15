@extends('dashboard.layouts.main')

@section('container')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">User Management</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="search-input" class="form-control" placeholder="Search by name, username, or email">
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-warning">Add User</a>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body">
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <button type="button" class="btn {{ $user->status ? 'btn-success' : 'btn-warning' }} btn-sm">
                                        {{ $user->status ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">{{ $user->status ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="no-results" class="text-center text-muted" style="display: none;">
                    No users found in the database.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            var visible = false;

            $('#user-table-body tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                if ($(this).css('display') !== 'none') {
                    visible = true;
                }
            });

            $('#no-results').toggle(!visible);
        });
    });
</script>
@endsection
