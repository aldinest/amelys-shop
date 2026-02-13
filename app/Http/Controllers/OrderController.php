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

class OrderController extends Controller
{
    // List orders
    public function index(Request $request)

    {
        $query = Order::query();

        // Filter search
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                ->orWhere('customer_name', 'like', "%$search%")
                ->orWhere('e_commerce', 'like', "%$search%");
            });
        }

        // Filter status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter tanggal
        if (request('date_from') && request('date_to')) {
            $query->whereBetween('created_at', [
                request('date_from'),
                request('date_to')
            ]);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }


    // Show create form
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('orders.create', compact('products'));
    }

    // Store order + items
    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'e_commerce' => 'required',
            'order_number' => 'required',
            'customer_name' => 'required',
            'status' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $order = Order::create([
                'order_date'   => $request->order_date,
                'order_number' => $request->order_number,
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
        });

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil disimpan');
    }

    public function edit(Order $order)
    {
        $products = Product::orderBy('name')->get();

        $order->load('items.product');

        return view('orders.edit', compact('order','products'));
    }

    public function update(Request $request, Order $order)
{
    //dd($request->all());
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
        ->route('orders.show',$order->order_number)
        ->with('success','Order berhasil diupdate');
}

    public function show($order_number)
    {
        $order = Order::with(['items.product'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Pesanan berhasil dihapus');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new OrdersExport($request),
            'data-order.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::today()->startOfDay();

        $dateTo = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::today()->endOfDay();

    $items = DB::table('order_items')
        ->join('orders', 'orders.order_number', '=', 'order_items.order_number')
        ->join('products', 'products.id', '=', 'order_items.product_id')
        ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
        ->select(
            'products.name as product_name',
            'products.type as product_type',
            DB::raw('SUM(order_items.quantity) as total_qty')
        )
        ->groupBy('products.name', 'products.type')
        ->orderBy('products.name')
        ->get();

        return PDF::loadView('orders.export-pdf-harian', [
            'items'     => $items,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo
        ])->stream('laporan-penjualan.pdf');
    }


}
