@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">

            <section class="content-header">
                <div class="container-fluid">
                    <h1>Dashboard</h1>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">

                    {{-- INFO BOX --}}
                    <div class="row">

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalOrders }}</h3>
                                    <p>Total Pesanan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($totalRevenue, 0) }}</h3>
                                    <p>Total Pendapatan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $statusCount['pending'] }}</h3>
                                    <p>Pesanan Diproses</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $statusCount['completed'] }}</h3>
                                    <p>Pesanan Selesai</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- PESANAN TERBARU --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Pesanan Terbaru</h3>
                                </div>

                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>No. Pesanan</th>
                                                <th>Tanggal</th>
                                                <th>Pelanggan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentOrders as $order)
                                                <tr>
                                                    <td>{{ $order->order_number }}</td>
                                                    <td>{{ $order->order_date }}</td>
                                                    <td>{{ $order->customer_name }}</td>
                                                    <td>
                                                        <span class="badge
                                                            @if($order->status == 'pending') badge-warning
                                                            @elseif($order->status == 'processing') badge-info
                                                            @elseif($order->status == 'shipped') badge-primary
                                                            @else badge-success
                                                            @endif
                                                        ">
                                                            @if($order->status == 'pending')
                                                                Pesanan Diproses
                                                            @elseif($order->status == 'processing')
                                                                Sedang Diproses
                                                            @elseif($order->status == 'shipped')
                                                                Dikirim
                                                            @else
                                                                Selesai
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        Belum ada pesanan
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-footer text-right">
                                    <a href="{{ route('user.orders.index') }}" class="btn btn-sm btn-primary">
                                        Lihat Semua Pesanan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </section>
</div>
@endsection
