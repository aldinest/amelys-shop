@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">Laporan Riwayat Tahunan</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Periode</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.history.index') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group mb-0">
                                    <label>Tahun Laporan:</label>
                                    <select name="year" class="form-control select2" onchange="this.form.submit()">
                                        @php $currentYear = date('Y'); @endphp
                                        @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9 col-sm-6 mt-2 mt-md-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync-alt mr-1"></i> Update Data
                                </button>
                                
                                {{-- Tombol PDF Tahunan --}}
                                <!-- <div class="float-right d-none d-md-block">
                                    <button type="button" class="btn btn-outline-danger disabled">
                                        <i class="fas fa-file-pdf mr-1"></i> PDF Tahunan {{ $year }}
                                    </button>
                                </div> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info shadow">
                        <div class="inner">
                            <h3>{{ collect($monthlySummary)->sum('total_order') }}</h3>
                            <p>Total Pesanan {{ $year }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-bag"></i></div>
                    </div>
                </div>
                <div class="col-lg-8 col-6">
                    <div class="small-box bg-success shadow">
                        <div class="inner">
                            <h3>Rp {{ number_format(collect($monthlySummary)->sum('total_bersih')) }}</h3>
                            <p>Total Keuntungan Bersih {{ $year }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-default shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i> Perbandingan Omzet vs Profit Bulanan ({{ $year }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="position-relative" style="height: 320px;">
                        <canvas id="yearlyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="fas fa-table mr-1"></i> Ringkasan Performa Tiap Bulan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover m-0">
                            <thead>
                                <tr class="bg-light">
                                    <th>Bulan</th>
                                    <th class="text-center">Order</th>
                                    <th class="text-right">Total Kotor (Gross)</th>
                                    <th class="text-right">Total Bersih (Net)</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlySummary as $row)
                                <tr>
                                    <td><strong>{{ $row['month_name'] }}</strong></td>
                                    <td class="text-center"><span class="badge badge-pill badge-secondary">{{ $row['total_order'] }}</span></td>
                                    <td class="text-right text-muted">Rp {{ number_format($row['total_kotor']) }}</td>
                                    <td class="text-right">
                                        <span class="text-success font-weight-bold">
                                            Rp {{ number_format($row['total_bersih']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($row['total_order'] > 0)
                                            <a href="{{ route('user.history.export_pdf', ['month' => sprintf('%02d', $row['month_num']), 'year' => $year]) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-file-pdf mr-1"></i> PDF
                                            </a>
                                        @else
                                            <span class="text-muted small"><em>No Transaction</em></span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-dark text-white">
                                <tr class="font-weight-bold">
                                    <td>TOTAL {{ $year }}</td>
                                    <td class="text-center">{{ collect($monthlySummary)->sum('total_order') }}</td>
                                    <td class="text-right">Rp {{ number_format(collect($monthlySummary)->sum('total_kotor')) }}</td>
                                    <td class="text-right text-warning">Rp {{ number_format(collect($monthlySummary)->sum('total_bersih')) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxYearly = document.getElementById('yearlyChart').getContext('2d');
        new Chart(ctxYearly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Pendapatan Kotor',
                        data: {!! json_encode($chartDataKotor) !!},
                        backgroundColor: 'rgba(108, 117, 125, 0.2)', // Abu-abu transparan
                        borderColor: 'rgba(108, 117, 125, 0.8)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pendapatan Bersih',
                        data: {!! json_encode($chartDataBersih) !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.7)', // Hijau sukses
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { drawBorder: false, color: '#f2f2f2' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush