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

        .main-container { width: 100%; }

        .order-wrapper {
            width: 49%;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 4px;
        }

        .order-box {
            border: 1px solid #000;
            padding: 6px;
            margin: 2px;
            background-color: #fff;
            page-break-inside: avoid;
        }

        .order-header {
            font-weight: bold;
            border-bottom: 0.5px solid #ccc;
            margin-bottom: 5px;
            padding-bottom: 3px;
            font-size: 7.5px;
        }

        /* Container kanan untuk E-commerce & Status */
        .header-right {
            float: right;
            text-align: right;
        }

        .ecommerce-label {
            color: #555;
            font-weight: normal;
            margin-right: 5px;
        }

        .status-badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6.5px;
            color: #fff;
        }

        .meta { margin-bottom: 4px; line-height: 1.4; }
        
        .item-row { 
            clear: both;
            border-bottom: 0.1px solid #f9f9f9;
            padding: 2px 0;
            position: relative; /* Penting untuk posisi harga */
            min-height: 12px;
        }

        .item-price { 
            float: right; 
            font-weight: bold;
            margin-left: 10px; /* Jeda minimal antara teks produk dan harga */
            background: #fff; /* Menutupi garis jika teks produk terlalu panjang */
        }

        /* Tambahkan ini agar teks produk tidak menabrak harga */
        .product-name {
            display: block;
            margin-right: 60px; /* Beri ruang kosong di kanan khusus untuk tempat harga */
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .total-row {
            border-top: 1px dashed #999;
            margin-top: 5px;
            padding-top: 2px;
            text-align: right;
            font-weight: bold;
            font-size: 8.5px;
        }

        .payout-section {
            margin-top: 3px;
            text-align: right;
            font-size: 7px;
        }
        .text-cair { color: #0056b3; }
        .text-bersih { color: #218838; font-weight: bold; }

        .footer { position: fixed; bottom: -20px; width: 100%; text-align: center; font-size: 7px; color: #999; }
        .footer .page-number:after { content: "Halaman " counter(page); }

        .grand-summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1.5px solid #000;
            clear: both;
        }

        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

<div class="footer">
    <span class="page-number"></span>
</div>

<div style="text-align: center; margin-bottom: 12px;">
    <h2 style="margin:0; font-size: 13px; text-transform: uppercase;">Laporan Penjualan Amelys</h2>
    <p style="margin:2px 0; font-size: 9px; color: #555;">Periode: {{ request('date_from') }} s/d {{ request('date_to') }}</p>
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
            
            // Warna status
            $statusBg = $order->status == 'completed' ? '#218838' : '#e67e22';
        @endphp

        <div class="order-wrapper">
            <div class="order-box">
                <div class="order-header clearfix">
                    <div class="header-right">
                        <span class="ecommerce-label">[{{ strtoupper($order->e_commerce) }}]</span>
                        <span class="status-badge" style="background-color: {{ $statusBg }};">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                    #{{ $order->order_number }}
                </div>

                <div class="meta">
                    <strong>Pelanggan:</strong> {{ Str::limit($order->customer_name, 25) }}<br>
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y') }}
                </div>

                <div class="items">
                    @foreach ($order->items as $item)
                        <div class="item-row clearfix">
                            <span class="item-price">{{ number_format($item->sub_total) }}</span>
                            
                            <span class="product-name">
                                • {{ $item->product->name }} ({{ $item->quantity }}x)
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="total-row">
                    Total Order: Rp {{ number_format($orderTotal) }}
                </div>

                @if ($order->status === 'completed')
                    <div class="payout-section">
                        <span class="text-cair">Uang Cair: Rp {{ number_format($order->net_payout) }}</span> 
                        <span style="color: #ccc;"> | </span>
                        <span class="text-bersih">Bersih: Rp {{ number_format($order->net_total) }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="grand-summary">
    <h4 style="margin:0 0 8px 0; border-bottom: 1px solid #000; font-size: 10px;">REKAPITULASI TOTAL</h4>
    <table width="100%" style="font-size: 9px; border-collapse: collapse;">
        <tr>
            <td style="padding: 2px 0;">Total Pesanan Dicetak:</td>
            <td align="right"><strong>{{ count($orders) }} Order</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Total Omzet (Kotor):</td>
            <td align="right"><strong>Rp {{ number_format($totalKotor) }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;" class="text-cair">Total Dana Cair (Completed):</td>
            <td align="right" class="text-cair"><strong>Rp {{ number_format($totalCair) }}</strong></td>
        </tr>
        <tr style="font-size: 12px;">
            <td style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;" class="text-bersih">TOTAL HASIL BERSIH:</td>
            <td align="right" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;" class="text-bersih"><strong>Rp {{ number_format($totalBersih) }}</strong></td>
        </tr>
    </table>
</div>

</body>
</html>