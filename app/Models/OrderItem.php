<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'product_variant_id', 'product_name',
        'quantity', 'unit_price', 'subtotal'
    ];
}
