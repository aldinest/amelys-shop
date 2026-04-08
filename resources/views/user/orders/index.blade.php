@extends('layouts.main')

@section('content')

<style>
    /* Mengatasi bug teks turun ke bawah */
    .text-nowrap {
        white-space: nowrap !important;
    }

    /* Membuat jarak antar tombol seragam */
    .gap-2 {
        gap: 0.5rem !important;
    }

    /* Memastikan semua input & button punya tinggi yang sama */
    .form-control-sm, 
    .btn-sm, 
    .input-group-text,
    .dropdown-toggle {
        height: 31px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Khusus untuk dropdown agar teks e-commerce rata kiri */
    .dropdown-toggle {
        justify-content: space-between;
    }

    /* Jarak vertikal saat layar mengecil (mobile) */
    @media (max-width: 768px) {
        .mb-1 {
            margin-bottom: 0.5rem !important;
        }
    }
</style>

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

                        <a href="{{ route('user.orders.export.excel', request()->query()) }}"
                        class="btn btn-success btn-sm">
                            Excel
                        </a>

                        <a href="{{ route('user.orders.export.pdf', request()->query()) }}"
                        class="btn btn-danger btn-sm">
                            Cetak PDF Stok
                        </a>

                        <a href="{{ route('user.orders.print', request()->query()) }}"
                        target="_blank"
                        class="btn btn-primary btn-sm">
                        Cetak PDF All
                        </a>
                        
                        <a href="{{ route('user.orders.create') }}"
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
<div class="col-md-9 col-12">
    <form method="GET" action="{{ route('user.orders.index') }}">
        <div class="row gx-2 gy-2 justify-content-end align-items-center">
            
            {{-- Search --}}
            <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari..." class="form-control form-control-sm">
            </div>

            {{-- E-Commerce --}}
            <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-1">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-left d-flex justify-content-between align-items-center" 
                        type="button" id="dropdownEcom" data-toggle="dropdown">
                        <span class="text-truncate">E-Commerce</span>
                    </button>
                    <div class="dropdown-menu p-3 shadow" style="min-width: 200px;">
                        @php $selectedEcoms = request('e_commerce', []); @endphp
                        @foreach(['Shopee', 'WhatsApp', 'Tokopedia', 'TikTok'] as $ecom)
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" name="e_commerce[]" value="{{ $ecom }}" 
                                    class="custom-control-input" id="check-{{ $ecom }}"
                                    {{ in_array($ecom, $selectedEcoms) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal" for="check-{{ $ecom }}">
                                    {{ $ecom }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-1">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Semua Status</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Proses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="col-xl-4 col-lg-6 col-md-6 col-12 mb-1">
                <div class="input-group input-group-sm">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    <div class="input-group-append">
                        <span class="input-group-text px-2">s/d</span>
                    </div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>

            {{-- Buttons (Perbaikan di sini) --}}
            <div class="col-xl-2 col-lg-6 col-md-6 col-12 mb-1">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm flex-fill text-nowrap shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('user.orders.index') }}" class="btn btn-outline-secondary btn-sm flex-fill text-nowrap shadow-sm">
                        Reset
                    </a>
                </div>
            </div>

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
                                            @if($order->e_commerce == 'WhatsApp')
                                                {{-- Untuk WA, tampilkan Net Total (yang sudah otomatis setara Grand Total) --}}
                                                    Rp {{ number_format($order->net_total ?? 0, 0, ',', '.') }}
                                            @else
                                                {{-- Untuk E-Commerce lain, tetap tampilkan net_total (yang diisi saat input uang cair) --}}
                                                Rp {{ number_format($order->net_total ?? 0, 0, ',', '.') }}
                                            @endif
                                        </td>

                                        <td class="text-center">

                                            <a href="{{ route('user.orders.show', $order) }}"
                                               class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('user.orders.edit', $order) }}?{{ http_build_query(request()->query()) }}"
                                            class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('user.orders.destroy', $order) }}"
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
                    <div class="card-footer d-flex align-items-center">

                        <small class="text-muted">
                            Menampilkan
                            {{ $orders->firstItem() ?? 0 }}
                            -
                            {{ $orders->lastItem() ?? 0 }}
                            dari
                            {{ $orders->total() }}
                            data
                        </small>
                        <div class="ml-auto">
                        {{ $orders->links() }}
                    </div>

                    </div>

                </div>
            </div>
        </section>

    </div>

@endsection
