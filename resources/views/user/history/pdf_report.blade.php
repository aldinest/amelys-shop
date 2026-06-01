<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 1cm; }
        
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            color: #333;
            line-height: 1.2;
        }

        .header-title {
            text-align: center;
            margin-bottom: 15px;
        }

        .header-title h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .main-container { width: 100%; }

        /* Layout 2 Kolom */
        .order-wrapper {
            width: 49%;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 5px;
        }

        .order-box {
            border: 1px solid #000;
            padding: 6px;
            margin: 2px;
            background-color: #fff;
            page-break-inside: avoid; /* Mencegah box terpotong antar halaman */
        }

        .order-header {
            font-weight: bold;
            border-bottom: 0.5px solid #ccc;
            margin-bottom: 5px;
            padding-bottom: 3px;
        }

        .header-right {
            float: right;
            text-align: right;
        }

        .status-badge {
            padding: 1px 4px;
            color: #fff;
            border-radius: 2px;
            font-size: 6.5px;
        }

        .meta { margin-bottom: 5px; }

        .item-row { 
            clear: both;
            padding: 2px 0;
            position: relative;
            border-bottom: 0.1px solid #f2f2f2;
        }

        .item-price { 
            float: right; 
            font-weight: bold;
            margin-left: 5px;
        }

        .product-name {
            display: block;
            margin-right: 55px; /* Ruang untuk harga agar tidak tabrakan */
            word-wrap: break-word;
        }

        .total-row {
            border-top: 1px dashed #999;
            margin-top: 5px;
            padding-top: 2px;
            text-align: right;
            font-weight: bold;
        }

        .payout-section {
            margin-top: 3px;
            text-align: right;
            font-size: 7px;
        }

        .text-cair { color: #0056b3; } /* Biru */
        .text-bersih { color: #218838; font-weight: bold; } /* Hijau */

        .grand-summary {
            margin-top: 20px;
            padding: 10px;
            border: 1.5px solid #000;
            background-color: #f9f9f9;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            width: 100%;
            text-align: center;
            font-size: 7px;
            color: #999;
        }
        .footer .page-number:after { content: "Halaman " counter(page); }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

<div class="footer">
    <span class="page-number"></span>
</div>

<div class="header-title">
    <h2>Laporan Penjualan Amelys</h2>
    <p style="font-size: 9px;">Periode: {{ $monthName ?? date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
</div>

@php
    $totalKotor = 0;
    $totalCair = 0;
    $totalBersih = 0;
@endphp

<div class="main-container">
    @foreach ($orders as $order)
        @php
            $orderTotal = $order->items->sum('sub_total');
            $totalKotor += $orderTotal;
            if ($order->status === 'completed') {
                $totalCair += $order->net_payout;
                $totalBersih += $order->net_total;
            }
            $statusColor = $order->status == 'completed' ? '#218838' : '#e67e22';
        @endphp

        <div class="order-wrapper">
            <div class="order-box">
                <div class="order-header clearfix">
                    <div class="header-right">
                        <span style="color: #555; font-weight: normal; margin-right: 4px;">[{{ strtoupper($order->e_commerce) }}]</span>
                        <span class="status-badge" style="background-color: {{ $statusColor }};">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                    #{{ $order->order_number }}
                </div>

                <div class="meta">
                    <strong>Pelanggan:</strong> {{ $order->customer_name }}<br>
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y') }}
                </div>

                <div class="items">
                    @foreach ($order->items as $item)
                        <div class="item-row clearfix">
                            <span class="item-price">{{ number_format($item->sub_total) }}</span>
                            <span class="product-name">• {{ $item->product->name }} ({{ $item->quantity }}x)</span>
                        </div>
                    @endforeach
                </div>

                <div class="total-row">
                    Total Order: Rp {{ number_format($orderTotal) }}
                </div>

                @if ($order->status === 'completed')
                    <div class="payout-section">
                        <span class="text-cair">Cair: {{ number_format($order->net_payout) }}</span> | 
                        <span class="text-bersih">Bersih: {{ number_format($order->net_total) }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="grand-summary">
    <table width="100%" style="font-size: 10px; border-collapse: collapse;">
        <tr>
            <td colspan="2" style="border-bottom: 1px solid #000; padding-bottom: 5px;"><strong>REKAPITULASI BULANAN</strong></td>
        </tr>
        <tr>
            <td style="padding: 4px 0;">Total Pesanan:</td>
            <td align="right">{{ count($orders) }} Order</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Total Omzet (Kotor):</td>
            <td align="right">Rp {{ number_format($totalKotor) }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;" class="text-cair">Total Dana Cair:</td>
            <td align="right" class="text-cair">Rp {{ number_format($totalCair) }}</td>
        </tr>
        <tr style="font-size: 12px; font-weight: bold;">
            <td style="border-top: 1px solid #000; padding-top: 5px;" class="text-bersih">TOTAL HASIL BERSIH:</td>
            <td align="right" style="border-top: 1px solid #000; padding-top: 5px;" class="text-bersih">Rp {{ number_format($totalBersih) }}</td>
        </tr>
    </table>
</div>

</body>
</html>