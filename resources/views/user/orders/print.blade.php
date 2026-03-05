<!DOCTYPE html>
<html>
<head>
    <style>
        /* Konfigurasi Halaman & Nomor Halaman */
        @page {
            margin: 1.5cm;
        }
        
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        /* Penomoran Halaman (Khusus DomPDF/Cetak Browser) */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
        .footer .page-number:after {
            content: "Halaman " counter(page);
        }

        /* Mencegah Pesanan Terpotong (Page Break) */
        .order-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 12px;
            page-break-inside: avoid; /* Sangat Penting! */
            break-inside: avoid;
        }

        .order-header {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            margin-bottom: 5px;
            padding-bottom: 3px;
        }

        .meta { margin-bottom: 4px; }
        .items { margin-left: 10px; }
        .item { display: flex; justify-content: space-between; }

        .total-row {
            border-top: 1px dashed #999;
            margin-top: 6px;
            padding-top: 4px;
            text-align: right;
            font-weight: bold;
        }

        /* Kotak Ringkasan Akhir */
        .grand-summary {
            margin-top: 25px;
            padding: 12px;
            background-color: #f9f9f9;
            border: 2px solid #000;
            page-break-inside: avoid;
        }
        .grand-summary h4 {
            margin-top: 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            text-transform: uppercase;
        }
        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

<div class="footer">
    <span class="page-number"></span>
</div>

<h2 style="text-align: center;">LAPORAN PENJUALAN AMELYS</h2>
<p style="text-align: center; margin-top: -10px;">Periode: {{ request('date_from') }} s/d {{ request('date_to') }}</p>

@php
    $totalKotor = 0;
    $totalCair = 0;
    $totalBersih = 0;
    $jumlahPesanan = count($orders);
@endphp

@foreach ($orders as $order)
    @php
        $orderTotal = $order->items->sum('sub_total');
        $totalKotor += $orderTotal;

        if ($order->status === 'completed') {
            $totalCair += $order->net_payout;
            $totalBersih += $order->net_total;
        }
    @endphp

    <div class="order-box">
        <div class="order-header">
            <span>#{{ $order->order_number }} [{{ strtoupper($order->e_commerce) }}]</span>
            <span>STATUS: {{ strtoupper($order->status) }}</span>
        </div>

        <div class="meta">
            <strong>Pelanggan:</strong> {{ $order->customer_name }} <br>
            <strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
        </div>

        <div class="items">
            @foreach ($order->items as $item)
                <div class="item">
                    <span>• {{ $item->product->name }} ({{ $item->quantity }}x)</span>
                    <span>Rp {{ number_format($item->sub_total) }}</span>
                </div>
            @endforeach
        </div>

        <div class="total-row">
            Total Order: Rp {{ number_format($orderTotal) }}
        </div>

        @if ($order->status === 'completed')
            <div style="margin-top: 5px; text-align: right; font-size: 9px; color: #444;">
                <span>Uang Cair: Rp {{ number_format($order->net_payout) }}</span> | 
                <strong>Bersih: Rp {{ number_format($order->net_total) }}</strong>
            </div>
        @endif
    </div>
@endforeach

<div class="grand-summary">
    <h4>REKAPITULASI TOTAL</h4>
    <div class="summary-line">
        <span>Total Pesanan Dicetak:</span>
        <strong>{{ $jumlahPesanan }} Order</strong>
    </div>
    <div class="summary-line">
        <span>Total Omzet (Kotor):</span>
        <strong>Rp {{ number_format($totalKotor) }}</strong>
    </div>
    <div class="summary-line" style="color: #2c3e50;">
        <span>Total Dana Cair (Completed):</span>
        <strong>Rp {{ number_format($totalCair) }}</strong>
    </div>
    <div class="summary-line" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; font-size: 14px;">
        <span>TOTAL HASIL BERSIH:</span>
        <strong>Rp {{ number_format($totalBersih) }}</strong>
    </div>
</div>

</body>
</html>