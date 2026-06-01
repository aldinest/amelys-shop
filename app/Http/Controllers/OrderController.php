<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Helper untuk filter data agar konsisten antara Index dan Print
     */
    private function applyFilters($query, Request $request)
    {
        // Filter massal via checkbox (Jika ada, langsung batasi data terpilih saja)
        if ($request->filled('selected_ids')) {
            $ids = array_filter(explode(',', $request->selected_ids));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        // Filter search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                ->orWhere('customer_name', 'like', "%$search%")
                ->orWhere('e_commerce', 'like', "%$search%");
            });
        }

        // Cek jika request e_commerce ada dan merupakan array
        if ($request->has('e_commerce') && is_array($request->e_commerce)) {
            $query->whereIn('e_commerce', $request->e_commerce);
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // FITUR BARU: Filter Berdasarkan Status Cetak
        if ($request->filled('print_status')) {
            if ($request->print_status === 'sudah') {
                $query->where('is_printed', true);
            } elseif ($request->print_status === 'belum') {
                $query->where('is_printed', false);
            }
        }

        // Filter tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('order_date', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = Order::query();
        $query = $this->applyFilters($query, $request);

        $orders = $query->latest()->paginate(10)->withQueryString();
        return view('user.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('user.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_date'         => 'required|date',
            'e_commerce'         => 'required',
            'order_number'       => $request->e_commerce === 'WhatsApp' ? 'nullable' : 'required|unique:orders,order_number',
            'customer_name'      => 'required',
            'status'             => 'required',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            // LOGIKA GENERATE NOMOR OTOMATIS
            $orderNumber = $request->order_number;
            if ($request->e_commerce === 'WhatsApp') {
                $orderNumber = 'WA-' . date('Ymd-His');
            }

            $order = Order::create([
                'order_date'   => $request->order_date,
                'order_number' => $orderNumber,
                'customer_name'=> $request->customer_name,
                'e_commerce'   => $request->e_commerce,
                'status'       => $request->status,
                'gross_total'  => $request->net_total ?? 0, 
                'net_total'    => $request->net_total ?? 0,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_number' => $order->order_number,
                    'product_id'   => $item['product_id'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $item['qty'],
                    'sub_total'    => $item['price'] * $item['qty'],
                ]);
            }

            return redirect()
                ->route('user.orders.index', request()->query())
                ->with('success', "Order $orderNumber berhasil disimpan");
        });
    }

    public function edit(Order $order)
    {
        $products = Product::orderBy('name')->get();
        $order->load('items.product');
        return view('user.orders.edit', compact('order','products'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_date'    => 'required|date',
            'e_commerce'    => 'required|string',
            'customer_name' => 'required|string',
            'status'        => 'required|string',
            'net_payout'    => 'nullable|numeric',
        ]);

        $grossTotal = $order->items()->sum('sub_total');

        $order->update([
            'order_date'    => $request->order_date,
            'e_commerce'    => $request->e_commerce,
            'customer_name' => $request->customer_name,
            'status'        => $request->status,
            'gross_total'   => $grossTotal,
            'net_payout'    => $request->net_payout ?? 0,
            'net_total'     => $grossTotal - ($request->net_payout ?? 0),
        ]);

        return redirect()->route('user.orders.index', $request->query())
        ->with('success', 'Order berhasil diupdate');
    }

    public function show($order_number)
    {
        $order = Order::with(['items.product'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        return view('user.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('user.orders.index', $request->query())
        ->with('success', 'Pesanan berhasil dihapus');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new OrdersExport($request), 'data-order.xlsx');
    }

    public function print(Request $request)
    {
        $query = Order::with(['items.product']);
        $query = $this->applyFilters($query, $request);

        $orders = $query->get();

        return Pdf::loadView('user.orders.print', compact('orders'))
            ->setPaper('A4', 'portrait')
            ->stream('orders.pdf');
    }

    /**
     * METHOD BARU: Menangani cetak massal nota dari data pilihan checkbox index
     */
    // Terapkan logika penanganan ini di fungsi Print, Excel, maupun PDF Stok kamu
    public function printMassal(Request $request)
    {
        // LOGIKA A: JIKA ADMIN MENCENTANG DATA (Checkbox Menang Mutlak)
        if ($request->has('selected_ids') && !empty($request->selected_ids)) {
            $ids = explode(',', $request->selected_ids);
            
            // Tarik data yang dicentang saja, abaikan filter lain
            $orders = Order::whereIn('order_number', $ids)->get();
        } 
    // LOGIKA B: JIKA TIDAK ADA CENTANGAN (Gunakan Filter Bawaan di Layar)
        else {
            $query = Order::query();

            // 1. Filter Search (No Order / Customer)
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->search . '%');
                });
            }

            // 2. Filter E-Commerce (Shopee, Tokopedia, dll)
            if ($request->has('e_commerce') && !empty($request->e_commerce)) {
                $query->whereIn('e_commerce', $request->e_commerce);
            }

            // 3. Filter Status Pesanan (Processing, Completed, dll)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // 4. Filter Status Cetak (Sudah / Belum)
            if ($request->filled('print_status')) {
                $statusCetak = ($request->print_status == 'sudah') ? 1 : 0;
                $query->where('is_printed', $statusCetak);
            }

            // 5. Filter Rentang Tanggal
            if ($request->filled('date_from')) {
                $query->where('order_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('order_date', '<=', $request->date_to);
            }

            $orders = $query->get();
        }

        // --- DI SINI LOGIKA UPDATE STATUS IS_PRINTED ---
        // Sebelum data di-render, tandai data tersebut sebagai "Sudah Dicetak" (1)
        if ($orders->count() > 0) {
            // Gunakan order_number sebagai filter dan pluck data
            Order::whereIn('order_number', $orders->pluck('order_number'))->update(['is_printed' => 1]);
        }

        // Return ke file cetakan PDF / View kamu masing-masing
        $pdf = \PDF::loadView('user.orders.print', compact('orders'));
        return $pdf->stream('cetak-massal.pdf');
        }

   public function exportPdf(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfDay();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();

        // 1. Inisialisasi Query dasar
        $query = DB::table('order_items')
            ->join('orders', 'orders.order_number', '=', 'order_items.order_number')
            ->join('products', 'products.id', '=', 'order_items.product_id');

        // 2. Logika jika ada ID yang dicentang (Checkbox)
        if ($request->filled('selected_ids')) {
            $ids = array_filter(explode(',', $request->selected_ids));
            
            // --- VALIDASI: Cek apakah ada status selain 'completed' ---
            $invalidOrders = DB::table('orders')
                ->whereIn('order_number', $ids)
                ->where('status', '!=', 'completed')
                ->exists();

            if ($invalidOrders) {
                return back()->with('error', 'Gagal! Anda mencentang pesanan yang belum selesai (status bukan Completed). Hanya pesanan Completed yang bisa dicetak.');
            }

            $query->whereIn('orders.order_number', $ids);
        } 
        // 3. Logika filter tanggal (Jika tidak ada checkbox)
        else {
            $query->where('orders.status', 'completed')
                ->whereBetween('orders.order_date', [$dateFrom, $dateTo]);
        }

        // 4. Eksekusi perhitungan (DIREVISI)
        $items = $query->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                // Kita gunakan net_payout sekarang
                DB::raw('SUM(order_items.sub_total * (orders.net_payout / NULLIF(orders.gross_total, 0))) as total_payout')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('products.name')
            ->get();

        // 5. Kembalikan ke View
        return Pdf::loadView('user.orders.export-pdf-harian', compact('items', 'dateFrom', 'dateTo'))
            ->stream('laporan-penjualan-cair.pdf');
    }

}