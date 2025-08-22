<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\CustomerPurchase;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display customer purchases
     */
    public function customerPurchases(Request $request, $customerId)
    {
        try {
            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Customer not found'
                    ]
                ], 404);
            }

            $query = $customer->purchases()->with('items.product');

            // Date range filter
            if ($request->filled('from')) {
                $query->where('purchase_date', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->where('purchase_date', '<=', $request->to);
            }

            $purchases = $query->orderBy('purchase_date', 'desc')->paginate(20);

            return response()->json([
                'data' => $purchases->items(),
                'meta' => [
                    'current_page' => $purchases->currentPage(),
                    'total' => $purchases->total(),
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'phone_raw' => $customer->phone_raw,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch customer purchases',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Store a new purchase for customer
     */
    public function store(StorePurchaseRequest $request, $customerId)
    {
        try {
            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Customer not found'
                    ]
                ], 404);
            }

            DB::beginTransaction();

            // Create purchase
            $purchase = CustomerPurchase::create([
                'customer_id' => $customer->id,
                'purchase_date' => $request->purchase_date ?? now(),
                'total_amount' => 0, // Will calculate from items
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;

            // Create purchase items
            foreach ($request->items as $itemData) {
                $product = null;
                if (isset($itemData['product_id'])) {
                    $product = Product::find($itemData['product_id']);
                }

                $quantity = $itemData['quantity'];
                $priceEach = $itemData['price_each'];
                $subtotal = $quantity * $priceEach;
                $totalAmount += $subtotal;

                $purchase->items()->create([
                    'product_id' => $product ? $product->id : null,
                    'quantity' => $quantity,
                    'unit' => $itemData['unit'] ?? null,
                    'price_each' => $priceEach,
                ]);
            }

            // Update purchase total
            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Load relationships for response
            $purchase->load(['items.product', 'customer']);

            return response()->json([
                'message' => 'Purchase created successfully',
                'data' => $purchase
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => [
                    'code' => 'CREATE_ERROR',
                    'message' => 'Failed to create purchase',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified purchase
     */
    public function show($id)
    {
        try {
            $purchase = CustomerPurchase::with(['items.product', 'customer'])->find($id);
            
            if (!$purchase) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Purchase not found'
                    ]
                ], 404);
            }

            return response()->json(['data' => $purchase]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch purchase',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }
}