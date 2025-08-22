<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = ProductGroup::query();

            // Filter by active status
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            // Search by name
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $productGroups = $query->select('id', 'name', 'description', 'is_active', 'created_by', 'created_at', 'updated_at')
                                  ->paginate($perPage);

            // Add products count to each group
            $data = $productGroups->getCollection()->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'is_active' => $group->is_active,
                    'created_by' => $group->created_by,
                    'created_at' => $group->created_at,
                    'updated_at' => $group->updated_at,
                    'products_count' => ProductGroup::find($group->id)->products()->count()
                ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'current_page' => $productGroups->currentPage(),
                    'per_page' => $productGroups->perPage(),
                    'total' => $productGroups->total(),
                    'last_page' => $productGroups->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch product groups',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:product_groups,name',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => [
                        'code' => 'VALIDATION_ERROR',
                        'message' => 'Validation failed',
                        'fields' => $validator->errors()
                    ]
                ], 422);
            }

            $productGroup = ProductGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', true),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Product group created successfully',
                'data' => [
                    'id' => $productGroup->id,
                    'name' => $productGroup->name,
                    'description' => $productGroup->description,
                    'is_active' => $productGroup->is_active,
                    'created_by' => $productGroup->created_by,
                    'created_at' => $productGroup->created_at,
                    'updated_at' => $productGroup->updated_at,
                    'products_count' => 0
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'CREATE_ERROR',
                    'message' => 'Failed to create product group',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $productGroup = ProductGroup::find($id);
            
            if (!$productGroup) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product group not found'
                    ]
                ], 404);
            }

            // Get products for this group
            $products = $productGroup->products()
                                   ->select('id', 'name', 'sku', 'price_default', 'is_active')
                                   ->get();

            $data = [
                'id' => $productGroup->id,
                'name' => $productGroup->name,
                'description' => $productGroup->description,
                'is_active' => $productGroup->is_active,
                'created_by' => $productGroup->created_by,
                'created_at' => $productGroup->created_at,
                'updated_at' => $productGroup->updated_at,
                'products_count' => $products->count(),
                'products' => $products
            ];

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch product group',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $productGroup = ProductGroup::find($id);
            
            if (!$productGroup) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product group not found'
                    ]
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:product_groups,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => [
                        'code' => 'VALIDATION_ERROR',
                        'message' => 'Validation failed',
                        'fields' => $validator->errors()
                    ]
                ], 422);
            }

            $productGroup->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', $productGroup->is_active),
            ]);

            return response()->json([
                'message' => 'Product group updated successfully',
                'data' => [
                    'id' => $productGroup->id,
                    'name' => $productGroup->name,
                    'description' => $productGroup->description,
                    'is_active' => $productGroup->is_active,
                    'created_by' => $productGroup->created_by,
                    'created_at' => $productGroup->created_at,
                    'updated_at' => $productGroup->updated_at,
                    'products_count' => $productGroup->products()->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'UPDATE_ERROR',
                    'message' => 'Failed to update product group',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $productGroup = ProductGroup::find($id);
            
            if (!$productGroup) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Product group not found'
                    ]
                ], 404);
            }

            // Check if group has products
            $productsCount = $productGroup->products()->count();
            if ($productsCount > 0) {
                return response()->json([
                    'error' => [
                        'code' => 'HAS_PRODUCTS',
                        'message' => "Cannot delete product group that contains {$productsCount} products"
                    ]
                ], 422);
            }

            $groupName = $productGroup->name;
            $productGroup->delete();

            return response()->json([
                'message' => "Product group '{$groupName}' deleted successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'DELETE_ERROR',
                    'message' => 'Failed to delete product group',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }
}