<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>

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
                <th width="120">Jenis</th>
                <th width="110">Jumlah Terjual</th>
            </tr>
        </thead>

        <tbody>
            @php $grandTotal = 0; @endphp

            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="center">{{ ucfirst($item->product_type) }}</td>
                    <td class="center">{{ $item->total_qty }}</td>
                </tr>

                @php
                    $grandTotal += $item->total_qty;
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
                <td colspan="2">TOTAL ITEM</td>
                <td class="center">{{ $grandTotal }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
