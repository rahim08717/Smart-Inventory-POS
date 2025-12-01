<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    use HasFactory;

    protected $fillable = ['sales_return_id', 'product_variant_id', 'quantity', 'unit_price', 'refund_amount'];
}
