<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
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
            $query = Order::query();

            if ($this->request->search) {
                $query->where('order_number', 'like', '%' . $this->request->search . '%');
            }

            if ($this->request->status) {
                $query->where('status', $this->request->status);
            }

            if ($this->request->date_from && $this->request->date_to) {
                $query->whereBetween('created_at', [
                    $this->request->date_from,
                    $this->request->date_to
                ]);
            }

            return $query->get([
                'order_number',
                'created_at',
                'e_commerce',
                'customer_name',
                'status',
                'net_total'
            ]);
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
