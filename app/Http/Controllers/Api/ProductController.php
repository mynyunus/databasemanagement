<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with('productGroup:id,name');

            // Filter by product group
            if ($request->filled('group_id')) {
                $query->where('product_group_id', $request->group_id);
            }

            // Filter by active status
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            // Search by name or SKU
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $products = $query->paginate($perPage);

            // Transform data to include computed fields
            $data = $products->getCollection()->map(function($product) {
                return [
                    'id' => $product->id,
                    'product_group_id' => $product->product_group_id,
                    'product_group' => $product->productGroup,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'is_active' => $product->is_active,
                    'price_default' => $product->price_default,
                    'price_override' => $product->price_override,
                    'final_price' => $product->price_override ?? $product->price_default,
                    'cogs' => $product->cogs,
                    'postage_cost' => $product->postage_cost,
                    'bottle_qty' => $product->bottle_qty,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch products',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = Product::create([
                'product_group_id' => $request->product_group_id,
                'name' => $request->name,
                'sku' => strtoupper($request->sku), // Ensure SKU is uppercase
                'is_active' => $request->get('is_active', true),
                'price_default' => $request->price_default,
                'price_override' => $request->price_override,
                'cogs' => $request->cogs,
                'postage_cost' => $request->postage_cost,
                'bottle_qty' => $request->bottle_qty,
            ]);

            // Load relationship
            $product->load('productGroup:id,name');

            return response()->json([
                'message' => 'Product created successfully',
                'data' => [
                    'id' => $product->id,
                    'product_group_id' => $product->product_group_id,
                    'product_group' => $product->productGroup,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'is_active' => $product->is_active,
                    'price_default' => $product->price_default,
                    'price_override' => $product->price_override,
                    'final_price' => $product->price_override ?? $product->price_default,
                    'cogs' => $product->cogs,
                    'postage_cost' => $product->postage_cost,
                    'bottle_qty' => $product->bottle_qty,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'CREATE_ERROR',
                    'message' => 'Failed to create product',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        try {
            $product = Product::with('productGroup:id,name')->find($id);
            
            if (!$product) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product not found'
                    ]
                ], 404);
            }

            $data = [
                'id' => $product->id,
                'product_group_id' => $product->product_group_id,
                'product_group' => $product->productGroup,
                'name' => $product->name,
                'sku' => $product->sku,
                'is_active' => $product->is_active,
                'price_default' => $product->price_default,
                'price_override' => $product->price_override,
                'final_price' => $product->price_override ?? $product->price_default,
                'cogs' => $product->cogs,
                'postage_cost' => $product->postage_cost,
                'bottle_qty' => $product->bottle_qty,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch product',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product not found'
                    ]
                ], 404);
            }

            $product->update([
                'product_group_id' => $request->product_group_id,
                'name' => $request->name,
                'sku' => strtoupper($request->sku),
                'is_active' => $request->get('is_active', $product->is_active),
                'price_default' => $request->price_default,
                'price_override' => $request->price_override,
                'cogs' => $request->cogs,
                'postage_cost' => $request->postage_cost,
                'bottle_qty' => $request->bottle_qty,
            ]);

            // Load relationship
            $product->load('productGroup:id,name');

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => [
                    'id' => $product->id,
                    'product_group_id' => $product->product_group_id,
                    'product_group' => $product->productGroup,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'is_active' => $product->is_active,
                    'price_default' => $product->price_default,
                    'price_override' => $product->price_override,
                    'final_price' => $product->price_override ?? $product->price_default,
                    'cogs' => $product->cogs,
                    'postage_cost' => $product->postage_cost,
                    'bottle_qty' => $product->bottle_qty,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'UPDATE_ERROR',
                    'message' => 'Failed to update product',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product not found'
                    ]
                ], 404);
            }

            $productName = $product->name;
            $productSku = $product->sku;
            $product->delete();

            return response()->json([
                'message' => "Product '{$productName}' ({$productSku}) deleted successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'DELETE_ERROR',
                    'message' => 'Failed to delete product',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Toggle product active status
     */
    public function toggle($id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product not found'
                    ]
                ], 404);
            }

            $product->update(['is_active' => !$product->is_active]);

            return response()->json([
                'message' => 'Product status updated successfully',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'is_active' => $product->is_active,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'TOGGLE_ERROR',
                    'message' => 'Failed to toggle product status',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        try {
            $query = Product::with('productGroup:id,name');

            if ($request->filled('q')) {
                $search = $request->q;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            if ($request->filled('group_id')) {
                $query->where('product_group_id', $request->group_id);
            }

            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            $limit = min($request->get('limit', 20), 50);
            $products = $query->limit($limit)->get();

            $data = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'final_price' => $product->price_override ?? $product->price_default,
                    'is_active' => $product->is_active,
                    'product_group' => $product->productGroup,
                ];
            });

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'SEARCH_ERROR',
                    'message' => 'Failed to search products',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Quick list for product picker
     */
    public function quickList(Request $request)
    {
        try {
            $query = Product::with('productGroup:id,name')->where('is_active', true);

            $limit = min($request->get('limit', 20), 50);
            $products = $query->limit($limit)->get();

            $data = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'final_price' => $product->price_override ?? $product->price_default,
                    'product_group_name' => $product->productGroup->name,
                ];
            });

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch product quick list',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }
}