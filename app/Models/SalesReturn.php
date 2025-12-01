<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'customer_id', 'total_return_amount', 'return_date', 'note'];

    // Relationship with return items
    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    // Relationship with original order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
