<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Order::query()->with('items');


        // search
        if ($this->request->filled('search')) {
            $query->where('order_number', 'like', '%' . $this->request->search . '%');
        }

        // status
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        // filter tanggal (FIX)
        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $this->request->date_from . ' 00:00:00',
                $this->request->date_to . ' 23:59:59',
            ]);
        } elseif ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        } elseif ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('Y-m-d'),
            $order->e_commerce,
            $order->customer_name,
            $order->status,
            $order->items->sum('sub_total'), 
        ];
    }


        public function headings(): array
        {
            return [
                'No Order',
                'Tanggal',
                'E-Commerce',
                'Customer',
                'Status',
                'Total'
            ];
        }
    }
