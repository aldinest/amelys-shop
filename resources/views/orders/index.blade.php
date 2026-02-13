@extends('layouts.main')

@section('content')

    <div class="content-wrapper">

    {{-- ALERT --}}
    <div class="mx-3 mt-3">
        @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
            @if (session($key))
                <div class="alert alert-{{ $type }} alert-dismissible fade show">
                    {{ session($key) }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
        @endforeach
    </div>

        {{-- HEADER --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row align-items-center mb-2">

                    {{-- JUDUL --}}
                    <div class="col-md-6 col-12">
                        <h1 class="mb-0">Daftar Order</h1>
                    </div>

                    {{-- ACTION BUTTON --}}
                    <div class="col-md-6 col-12 d-flex justify-content-md-end gap-2 mt-2 mt-md-0 flex-wrap">

                        <a href="{{ route('orders.export.excel', request()->query()) }}"
                        class="btn btn-success btn-sm">
                            Excel
                        </a>

                        <a href="{{ route('orders.export.pdf', request()->query()) }}"
                        class="btn btn-danger btn-sm">
                            PDF
                        </a>

                        <a href="{{ route('orders.create') }}"
                        class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Order
                        </a>

                    </div>

                </div>
            </div>
        </section>


        {{-- CONTENT --}}
        <section class="content">
            <div class="container-fluid">
                <div class="card shadow-sm">

                    {{-- HEADER TABEL --}}
                    <div class="card-header">
                        <div class="row align-items-center">

                            {{-- JUDUL --}}
                            <div class="col-md-3 col-12 mb-2 mb-md-0">
                                <strong>Data Order</strong>
                            </div>

                            {{-- FILTER --}}
                            <div class="col-md-9 col-12 mb-2 mb-md-0">
                                <form method="GET"
                                    action="{{ route('orders.index') }}"
                                    class="row g-2 g-md-1">

                                    <div class="col-md-3 col-12 mb-2 mb-md-0">
                                        <input type="text"
                                            name="search"
                                            value="{{ request('search') }}"
                                            placeholder="Cari..."
                                            class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-2 col-12 mb-2 mb-md-0">
                                        <select name="status"
                                                class="form-control form-control-sm">
                                            <option value="">Semua Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-6 mb-2 mb-md-0">
                                        <input type="date"
                                            name="date_from"
                                            value="{{ request('date_from') }}"
                                            class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-2 col-6 mb-2 mb-md-0">
                                        <input type="date"
                                            name="date_to"
                                            value="{{ request('date_to') }}"
                                            class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-3 col-12 d-flex gap-2">
                                        <button class="btn btn-secondary btn-sm w-100">
                                            Filter
                                        </button>

                                        <a href="{{ route('orders.index') }}"
                                        class="btn btn-light btn-sm w-100">
                                            Reset
                                        </a>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">

                            <thead class="thead-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>No Order</th>
                                    <th>Tanggal</th>
                                    <th>E-Commerce</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-right">Total Bersih</th>
                                    <th class="text-center" width="140">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($orders as $i => $order)
                                    <tr>

                                        <td>{{ $orders->firstItem() + $i }}</td>

                                        <td>{{ $order->order_number }}</td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}
                                        </td>

                                        <td>{{ $order->e_commerce }}</td>
                                        <td>{{ $order->customer_name }}</td>

                                        <td>
                                            <span class="badge
                                                @if ($order->status == 'pending')
                                                    badge-warning
                                                @elseif($order->status == 'processing')
                                                    badge-info
                                                @elseif($order->status == 'shipped')
                                                    badge-primary
                                                @else
                                                    badge-success
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>

                                        <td class="text-right">
                                            Rp {{ number_format($order->net_total ?? 0, 0, ',', '.') }}
                                        </td>

                                        <td class="text-center">

                                            <a href="{{ route('orders.show', $order) }}"
                                               class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('orders.edit', $order) }}"
                                               class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('orders.destroy', $order) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus order ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-xs btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>

                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Tidak ada data order
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    {{-- FOOTER --}}
                    <div class="card-footer d-flex justify-content-between align-items-center">

                        <small class="text-muted">
                            Menampilkan
                            {{ $orders->firstItem() ?? 0 }}
                            -
                            {{ $orders->lastItem() ?? 0 }}
                            dari
                            {{ $orders->total() }}
                            data
                        </small>

                        {{ $orders->withQueryString()->links() }}

                    </div>

                </div>
            </div>
        </section>

    </div>

@endsection
