<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant_name', 'sku', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
    public function stock()
    {

        return $this->hasMany(Stock::class);
    }
}
