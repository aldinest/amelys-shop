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

<form action="{{ route('user.orders.update',$order->order_number) }}" method="POST">
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
<input type="text" class="form-control"
value="{{ $order->order_number }}" readonly>
</div>

<div class="form-group">
<label>Tanggal Order</label>
<input type="date" name="order_date"
class="form-control"
value="{{ old('order_date',$order->order_date) }}">
</div>

<div class="form-group">
<label>E-Commerce</label>
<input type="text" name="e_commerce"
class="form-control"
value="{{ old('e_commerce',$order->e_commerce) }}">
</div>

</div>

<div class="col-md-6">

<div class="form-group">
<label>Nama Customer</label>
<input type="text" name="customer_name"
class="form-control"
value="{{ old('customer_name',$order->customer_name) }}">
</div>

<div class="form-group">
<label>Status</label>
<select name="status" class="form-control">

<option value="pending"
{{ $order->status=='pending'?'selected':'' }}>
Pending
</option>

<option value="processing"
{{ $order->status=='processing'?'selected':'' }}>
Processing
</option>

<option value="shipped"
{{ $order->status=='shipped'?'selected':'' }}>
Shipped
</option>

<option value="completed"
{{ $order->status=='completed'?'selected':'' }}>
Completed
</option>

</select>
</div>

<div class="form-group">
<label>Uang Cair</label>
<input type="number"
name="net_payout"
class="form-control"
value="{{ old('net_payout',$order->net_payout ?? 0) }}">
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

@php
$subTotal = $order->items->sum('sub_total');
$payout   = $order->net_payout ?? 0;
$netTotal = $subTotal - $payout;
@endphp

<div class="card-body table-responsive p-0">
<table class="table table-bordered">
<thead class="text-center">
<tr>
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
<td class="text-right">
Rp {{ number_format($item->unit_price,0,',','.') }}
</td>
<td class="text-center">{{ $item->quantity }}</td>
<td class="text-right">
Rp {{ number_format($item->sub_total,0,',','.') }}
</td>
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
<td class="text-right">
Rp {{ number_format($subTotal,0,',','.') }}
</td>
</tr>

<tr>
<th>Total Uang Cair</th>
<td class="text-right">
Rp {{ number_format($payout,0,',','.') }}
</td>
</tr>

<tr class="font-weight-bold">
<th>Total Bersih</th>
<td class="text-right">
Rp {{ number_format($netTotal,0,',','.') }}
</td>
</tr>

</table>
</div>
</div>

<div class="card-footer">
    <div class="d-flex gap-2">

        <a href="{{ route('user.orders.index') }}"
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <button type="submit"
                class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>

    </div>
</div>


</div>

</form>

</div>
</section>

</div>
@endsection
