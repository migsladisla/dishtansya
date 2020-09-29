<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $order = $request->only(['product_id', 'quantity']);

        try {
            // Validate user authorization
            $user = auth()->userOrFail();
        } catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $error) {
            return response()->json(['message' => $error->getMessage()], 401);
        }

        // Get ordered product
        $orderedProduct = Product::find($order['product_id']);

        // Check if order is possible
        if ($orderedProduct['stock'] < $order['quantity']) {
            return response()->json(['message' => 'Failed to order this product due to unavailability of the stock'], 400);
        }

        try {
            // Register orders
            DB::beginTransaction();
            $user->orders()->create($order);
    
            $orderedProduct->stock = $orderedProduct['stock'] - $order['quantity'];
            $orderedProduct->save();

            DB::commit();
    
            return response()->json(['message' => 'You have successfully ordered this product'], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['message' => $error->getMessage()]);
        } catch (\Throwable $error) {
            DB::rollback();
            return response()->json(['message' => $error->getMessage()]);
        }
    }
}
