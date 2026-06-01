<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderHistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat pesanan dengan filter
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // 1. Data untuk Grafik & Tabel Ringkasan (Perbandingan tiap bulan dalam 1 tahun)
        // Kita grouping berdasarkan bulan
        // Bagian Query di Controller
        // Di dalam method index
        $yearlyReport = Order::whereYear('created_at', $year)
            ->where('status', 'completed')
            ->selectRaw('
                MONTH(created_at) as month, 
                SUM(net_total) as total_bersih, 
                SUM(gross_total) as total_kotor, 
                COUNT(*) as total_order
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartLabels = [];
        $chartDataBersih = [];
        $chartDataKotor = [];
        $monthlySummary = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthName = date('F', mktime(0, 0, 0, $m, 1));
            $chartLabels[] = $monthName;
            
            $bersih = $yearlyReport->has($m) ? $yearlyReport[$m]->total_bersih : 0;
            $kotor = $yearlyReport->has($m) ? $yearlyReport[$m]->total_kotor : 0;
            
            $chartDataBersih[] = $bersih;
            $chartDataKotor[] = $kotor;
            
            $monthlySummary[] = [
                'month_num' => $m,
                'month_name' => $monthName,
                'total_order' => $yearlyReport->has($m) ? $yearlyReport[$m]->total_order : 0,
                'total_bersih' => $bersih,
                'total_kotor' => $kotor
            ];
        }

        return view('user.history.index', compact('monthlySummary', 'chartLabels', 'chartDataBersih', 'chartDataKotor', 'year'));
    }

    /**
     * Fungsi untuk export ke PDF (2 Kolom)
     */
    public function exportPdf(Request $request)
{
    // Ambil bulan dan tahun dari request (default ke sekarang jika kosong)
    $month = $request->get('month', date('m'));
    $year = $request->get('year', date('Y'));

    // Ambil data order sesuai bulan dan tahun
    $orders = Order::with('items.product')
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->latest()
        ->get();

    // Kirim $month dan $year ke view
    $pdf = Pdf::loadView('user.history.pdf_report', compact('orders', 'month', 'year'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('Laporan_Penjualan_Amelys_' . $month . '_' . $year . '.pdf');
}
}
