<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    // 1. Show the Return Search Page
    public function create()
    {
        return view('returns.create');
    }

    // 2. Search for an Invoice
    public function search(Request $request)
    {
        $request->validate(['invoice_no' => 'required']);

        // Find order with items and customer info
        $order = Order::with(['items', 'customer'])
                      ->where('invoice_no', $request->invoice_no)
                      ->first();

        if (!$order) {
            return back()->with('error', 'Invoice not found!');
        }

        return view('returns.create', compact('order'));
    }

    // 3. Process the Return Request
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'return_items' => 'required|array', // Selected items to return
        ]);

        try {
            DB::beginTransaction();

            $totalRefund = 0;

            // A. Create the main Return Record
            $salesReturn = SalesReturn::create([
                'order_id' => $request->order_id,
                'customer_id' => $request->customer_id,
                'return_date' => now(),
                'note' => $request->note,
                'total_return_amount' => 0 // Will update later
            ]);

            // B. Loop through selected items
            foreach ($request->return_items as $variant_id => $qty) {
                // Skip if quantity is 0 or null
                if ($qty <= 0) continue;

                // Find the original price from order items (or request)
                // For simplicity, passing price from hidden input
                $unit_price = $request->prices[$variant_id];
                $refund_amount = $unit_price * $qty;
                $totalRefund += $refund_amount;

                // Save Return Item
                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_variant_id' => $variant_id,
                    'quantity' => $qty,
                    'unit_price' => $unit_price,
                    'refund_amount' => $refund_amount
                ]);

                // [CRITICAL STEP] Increase Stock Back
                // We are adding stock back to the first warehouse found for this item
                // In advanced version, select specific warehouse
                $stock = Stock::where('product_variant_id', $variant_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $qty);
                }
            }

            // C. Update total refund amount
            $salesReturn->update(['total_return_amount' => $totalRefund]);

            // Optional: Adjust customer due if needed (Simple version: just record return)
            // If you want to decrease customer due, you can add logic here.

            DB::commit();

            return redirect()->route('returns.create')->with('success', 'Return processed successfully! Stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
