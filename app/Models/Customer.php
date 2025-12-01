<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone', 'address', 'total_points'];

    
public function orders()
{
    return $this->hasMany(Order::class);
}

public function payments()
{
    return $this->hasMany(CustomerPayment::class);
}
}
