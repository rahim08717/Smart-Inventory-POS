<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\ProductVariant; // We need variants to buy specific items
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // Show the purchase creation form
    public function create()
    {
        // Fetch all active suppliers
        $suppliers = Supplier::all();

        // Fetch all warehouses
        $warehouses = Warehouse::all();

        // Fetch product variants with their parent product name
        // We use 'with' to prevent N+1 query performance issues
        $products = ProductVariant::with('product')->get();

        return view('purchases.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request)
{
    // 1. Validate the incoming request data
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'purchase_date' => 'required|date',
        'items' => 'required|array|min:1', // At least one product must be added
    ]);

    try {
        // Start Database Transaction to ensure data integrity
        DB::beginTransaction();

        // 2. Create the main Purchase record
        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'warehouse_id' => $request->warehouse_id,
            'purchase_date' => $request->purchase_date,
            'reference_no' => $request->reference_no,
            'total_amount' => $request->total_amount,
        ]);

        // 3. Loop through each item to save details and update stock
        foreach ($request->items as $itemData) {

            // A. Save Purchase Item
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_variant_id' => $itemData['variant_id'],
                'quantity' => $itemData['quantity'],
                'unit_cost' => $itemData['unit_cost'],
                'subtotal' => $itemData['quantity'] * $itemData['unit_cost'],
            ]);

            // B. UPDATE STOCK (Crucial Step)
            // Check if this product already exists in the selected warehouse
            $stock = Stock::where('warehouse_id', $request->warehouse_id)
                          ->where('product_variant_id', $itemData['variant_id'])
                          ->first();

            if ($stock) {
                // If stock exists, just increase the quantity
                $stock->increment('quantity', $itemData['quantity']);
            } else {
                // If stock doesn't exist, create a new record
                Stock::create([
                    'warehouse_id' => $request->warehouse_id,
                    'product_variant_id' => $itemData['variant_id'],
                    'quantity' => $itemData['quantity'],
                ]);
            }
        }

        // Commit the transaction if everything is fine
        DB::commit();

        return response()->json(['success' => true, 'message' => 'Purchase completed & Stock updated!']);

    } catch (\Exception $e) {
        // Rollback transaction if any error occurs
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
}
}
