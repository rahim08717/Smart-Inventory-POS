<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = ['supplier_id', 'warehouse_id', 'purchase_date', 'reference_no', 'total_amount'];


    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
