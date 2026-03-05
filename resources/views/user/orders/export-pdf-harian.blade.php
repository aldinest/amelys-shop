<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Harian</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
        }

        .periode {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td.center {
            text-align: center;
        }

        td.right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>LAPORAN PENJUALAN</h2>

    <div class="periode">
        Periode :
        {{ $dateFrom->format('d-m-Y') }}
        s/d
        {{ $dateTo->format('d-m-Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th width="110">Jumlah Terjual</th>
                <th width="140">Total Harga</th>
            </tr>
        </thead>

    <tbody>
        @php
            $totalQty = 0;
            $grandTotalPrice = 0;
        @endphp

        @forelse ($items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="center">{{ $item->total_qty }}</td>
                <td class="right">
                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                </td>
            </tr>

            @php
                $totalQty += $item->total_qty;
                $grandTotalPrice += $item->total_price;
            @endphp
        @empty
            <tr>
                <td colspan="3" style="text-align:center;">
                    Tidak ada penjualan pada periode ini
                </td>
            </tr>
        @endforelse
    </tbody>


        <tfoot>
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="center">{{ $totalQty }}</td>
                <td class="right">
                    Rp {{ number_format($grandTotalPrice, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
