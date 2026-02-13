@extends('layouts.main')

@section('content')
<div class="content-wrapper">

<section class="content-header">
    <div class="container-fluid">
        <h1>Detail Order</h1>
    </div>
</section>

<section class="content">
<div class="container-fluid">

{{-- INFO ORDER --}}
<div class="card card-primary">
<div class="card-header">
    <h3 class="card-title">Informasi Order</h3>
</div>

<div class="card-body">
<div class="row">

<div class="col-md-6">
    <p><strong>No Order:</strong> {{ $order->order_number }}</p>
    <p><strong>Tanggal Order:</strong> {{ $order->order_date }}</p>
    <p><strong>E-Commerce:</strong> {{ $order->e_commerce }}</p>
</div>

<div class="col-md-6">
    <p><strong>Nama Customer:</strong> {{ $order->customer_name }}</p>
    <p><strong>Status:</strong>
        <span class="badge
            @if($order->status=='pending') badge-warning
            @elseif($order->status=='processing') badge-info
            @elseif($order->status=='shipped') badge-primary
            @else badge-success
            @endif
        ">
            @if($order->status=='pending')
                Pesanan Diproses
            @elseif($order->status=='processing')
                Sedang Diproses
            @elseif($order->status=='shipped')
                Dikirim
            @else
                Selesai
            @endif
        </span>
    </p>

    <p><strong>Uang Cair:</strong>
        Rp {{ number_format($order->net_payout ?? 0,0,',','.') }}
    </p>
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

<div class="card-footer d-flex gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <a href="{{ route('orders.edit', $order->order_number) }}"
       class="btn btn-warning">
        <i class="fas fa-edit"></i> Edit
    </a>
</div>

</div>

</div>
</section>
</div>
@endsection
