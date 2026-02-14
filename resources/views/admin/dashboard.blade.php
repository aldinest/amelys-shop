@extends('layouts.app')

@section('content')
<div class="content-wrapper">
<section class="content pt-3">
<div class="container-fluid">

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<h3 class="mb-3">Data User</h3>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah User
        </a>

        <form method="GET" class="form-inline mb-0">
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control form-control-sm" placeholder="Search..." style="width: 200px">
            <button type="submit" class="btn btn-sm btn-secondary ml-2">Go</button>
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th width="120">Role</th>
                    <th width="160">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $i => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $i }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'pengurus' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-warning btn-sm text-white">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('Yakin mau hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Data tidak ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
        </div>
        <div>
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

</div>
</section>
</div>
@endsection
