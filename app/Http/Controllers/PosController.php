<?php

namespace App\Http\Controllers;

use App\Traits\SmsTrait;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    use SmsTrait;
    // Display the POS Interface
    public function index()
    {

        $products = Product::with(['variants.stock'])->latest()->get();


        $customers = Customer::all();


        return view('pos.index', compact('products', 'customers'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'customer_id' => 'required',
            'cart' => 'required|array',
            'total_amount' => 'required|numeric',
            'paid_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();


            $pointsUsed = $request->redeem_points ?? 0;
            $pointsDiscount = $pointsUsed * 1;

            $customer = \App\Models\Customer::find($request->customer_id);


            if ($pointsUsed > 0 && $customer) {
                if ($customer->total_points >= $pointsUsed) {
                    $customer->decrement('total_points', $pointsUsed);
                } else {
                    throw new \Exception("Not enough points!");
                }
            }


            $totalDiscount = ($request->discount ?? 0) + $pointsDiscount;

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'invoice_no' => 'INV-' . time(),
                'subtotal' => $request->subtotal,
                'discount' => $totalDiscount,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->total_amount - $request->paid_amount,
                'payment_method' => $request->payment_method,
            ]);


            foreach ($request->cart as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);


                $stock = Stock::where('product_variant_id', $item['id'])
                    ->where('quantity', '>=', $item['qty'])
                    ->first();

                if ($stock) {
                    $stock->decrement('quantity', $item['qty']);
                }
            }


            if ($customer) {
                $pointsEarned = floor($request->total_amount / 100);
                if ($pointsEarned > 0) {
                    $customer->increment('total_points', $pointsEarned);
                }
            }

            DB::commit();

            if ($customer && $customer->phone) {
            $msg = "Dear {$customer->name}, Thanks for shopping! Inv: {$order->invoice_no}. Total: {$request->total_amount} Tk. Paid: {$request->paid_amount} Tk.";

            // Trait এর ফাংশন কল করা হচ্ছে
            $this->sendSms($customer->phone, $msg);
        }
        // --- SMS পাঠানোর লজিক শেষ ---

        return response()->json([
            'success' => true,
            'message' => 'Order created!',
            'order_id' => $order->id
        ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function printInvoice($id)
    {

        $order = Order::with(['items', 'customer'])->findOrFail($id);

        return view('pos.print', compact('order'));
    }
}
