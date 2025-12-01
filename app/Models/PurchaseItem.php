<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItem extends Model
{
    use HasFactory;
    protected $fillable = ['purchase_id', 'product_variant_id', 'quantity', 'unit_cost', 'subtotal'];
}
