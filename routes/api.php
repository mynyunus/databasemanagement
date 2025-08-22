<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductGroupController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PurchaseController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
   // Auth
   Route::get('/auth/me', [AuthController::class, 'me']);
   Route::post('/auth/logout', [AuthController::class, 'logout']);

   // Product Groups
   Route::apiResource('product-groups', ProductGroupController::class);

   // Products
   Route::get('/products/search', [ProductController::class, 'search']);
   Route::get('/products/quick-list', [ProductController::class, 'quickList']);
   Route::apiResource('products', ProductController::class);
   Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle']);

   // Customers
   Route::apiResource('customers', CustomerController::class);

   // Purchases
   Route::get('/customers/{customer}/purchases', [PurchaseController::class, 'customerPurchases']);
   Route::post('/customers/{customer}/purchases', [PurchaseController::class, 'store']);
   Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']);

   // Debug routes
   Route::get('/debug-auth', function () {
       return response()->json([
           'status' => 'auth working',
           'user' => auth()->user(),
           'user_id' => auth()->id()
       ]);
   });
});

// Test route
Route::get('/test', function () {
   return response()->json(['message' => 'API is working!']);
});

// Debug route - add this temporarily
Route::get('/debug-groups', function () {
   try {
       return response()->json([
           'status' => 'working',
           'count' => \App\Models\ProductGroup::count(),
           'first_group' => \App\Models\ProductGroup::first(),
           'simple_list' => \App\Models\ProductGroup::select('id', 'name', 'is_active')->limit(3)->get()
       ]);
   } catch (\Exception $e) {
       return response()->json([
           'error' => $e->getMessage(),
           'line' => $e->getLine(),
           'file' => $e->getFile()
       ], 500);
   }
});