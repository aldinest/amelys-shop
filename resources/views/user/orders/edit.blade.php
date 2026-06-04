@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Order</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            {{-- Tambahkan query string ke action form sebagai cadangan --}}
            <form action="{{ route('user.orders.update', $order->order_number) }}?{{ http_build_query(request()->query()) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Order</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No Order</label>
                                    <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Order</label>
                                    <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $order->order_date) }}">
                                </div>
                                <div class="form-group">
                                    <label>E-Commerce</label>
                                    <select name="e_commerce" class="form-control">
                                        <option value="Shopee" {{ old('e_commerce', $order->e_commerce) == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                        <option value="WhatsApp" {{ old('e_commerce', $order->e_commerce) == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="Tokopedia" {{ old('e_commerce', $order->e_commerce) == 'Tokopedia' ? 'selected' : '' }}>Tokopedia</option>
                                        <option value="TikTok Shop" {{ old('e_commerce', $order->e_commerce) == 'TikTok Shop' ? 'selected' : '' }}>TikTok Shop</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Customer</label>
                                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $order->customer_name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Uang Cair</label>
                                    <input type="number" name="net_payout" class="form-control" value="{{ old('net_payout', $order->net_payout ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ITEM LIST --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Produk</h3>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i+1 }}</td>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <table class="table w-auto">
                                <tr>
                                    <th>Total Subtotal</th>
                                    <td class="text-right">Rp {{ number_format($order->items->sum('sub_total'), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Uang Cair</th>
                                    <td class="text-right">Rp {{ number_format($order->net_payout ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <th>Total Bersih</th>
                                    <td class="text-right">Rp {{ number_format($order->net_total ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('user.orders.index', request()->query()) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection