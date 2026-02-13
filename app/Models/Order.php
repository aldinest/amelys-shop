<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_number',
        'order_date',
        'e_commerce',
        'customer_name',
        'status',
        'gross_total',
        'net_payout',
        'net_total',
    ];

    // One order has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_number', 'order_number');
    }

    public function getGrossTotalCalculatedAttribute()
    {
        return $this->items->sum('sub_total');
    }

    public function getNetTotalAttribute()
    {
        return $this->gross_total - $this->net_payout;
    }

    public function getRouteKeyName()
    {
        return 'order_number';
    }



}
