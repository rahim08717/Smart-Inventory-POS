<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPayment extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'amount', 'payment_date', 'note'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
