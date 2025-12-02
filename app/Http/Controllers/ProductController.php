<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    public function index()
    {
        // Eager Loading (with 'variants') byabohar kora hoyeche jeno query fast hoy
        $products = Product::with('variants')->latest()->get();


        return view('products.index', compact('products'));
    }


    public function store(Request $request)
    {
        // ভ্যালিডেশন
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required',
            'variants.*.sku' => 'required|distinct|unique:product_variants,sku',
            'variants.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // ইমেজ হ্যান্ডলিং
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // প্রোডাক্ট তৈরি
            $product = Product::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'description' => $request->description,
                'image_path' => $imagePath,
            ]);

            // ভেরিয়েন্ট তৈরি
            foreach ($request->variants as $variantData) {
                $product->variants()->create([
                    'variant_name' => $variantData['name'],
                    'sku' => $variantData['sku'],
                    'price' => $variantData['price'],
                    'stock_quantity' => 0,
                ]);
            }

            DB::commit();
            //request check
           

            return response()->json(['success' => true, 'message' => 'Product added successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            // এরর লগ চেক করার জন্য
            Log::error('Product Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);

        if ($product->image_path) {
            $product->image_url = asset('storage/' . $product->image_path);
        }
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // ইমেজ আপডেট লজিক
            $imagePath = $product->image_path;
            if ($request->hasFile('image')) {
                // পুরনো ইমেজ ডিলিট
                if ($imagePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product->update([
                'name' => $request->name,
                'brand' => $request->brand,
                'description' => $request->description,
                'image_path' => $imagePath,
            ]);

            return response()->json(['success' => true, 'message' => 'Product updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);


            $product->delete();

            return response()->json(['success' => true, 'message' => 'Product deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting product.']);
        }
    }

    public function searchByBarcode(Request $request)
    {
        $barcode = $request->barcode;


        $variant = \App\Models\ProductVariant::where('sku', $barcode)
            ->with('product', 'stock')
            ->first();

        if ($variant) {
            return response()->json([
                'id' => $variant->id,
                'name' => $variant->product->name,
                'variant_name' => $variant->variant_name,
                'price' => $variant->price,
                'stock' => $variant->stock->sum('quantity')
            ]);
        }

        return response()->json(null);
    }

    private function handleImageUpload($request, $existingImagePath = null)
    {
        if ($request->hasFile('image')) {

            if ($existingImagePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingImagePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($existingImagePath);
            }


            return $request->file('image')->store('products', 'public');
        }


        return $existingImagePath;
    }
}
