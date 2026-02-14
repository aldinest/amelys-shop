<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'product_id',
        'unit_price',
        'quantity',
        'sub_total',
    ];

    // Each item belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }

    // Each item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
