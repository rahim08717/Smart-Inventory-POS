<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    // এই লাইনটি যোগ করুন
    protected $fillable = ['name', 'address', 'phone'];
}
