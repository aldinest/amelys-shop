<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    // One product can be in many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
