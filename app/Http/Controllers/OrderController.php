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
        // Filter search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                ->orWhere('customer_name', 'like', "%$search%")
                ->orWhere('e_commerce', 'like', "%$search%");
            });
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
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

        return redirect()
            ->route('user.orders.index', $request->redirect_query ?? [])
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
        return redirect()->back()->with('success', 'Pesanan berhasil dihapus');
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

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfDay();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();

        $items = DB::table('order_items')
            ->join('orders', 'orders.order_number', '=', 'order_items.order_number')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.sub_total) as total_price')
            )
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->groupBy('products.id', 'products.name')
            ->orderBy('products.name')
            ->get();

        return Pdf::loadView('user.orders.export-pdf-harian', compact('items', 'dateFrom', 'dateTo'))
            ->stream('laporan-penjualan.pdf');
    }
}