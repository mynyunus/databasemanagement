<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Search by name or phone
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone_raw', 'like', "%{$search}%")
                      ->orWhere('phone_e164', 'like', "%{$search}%");
                });
            }

            // Filter by state
            if ($request->filled('state')) {
                $query->where('state', $request->state);
            }

            // Pagination
            $perPage = min($request->get('per_page', 100), 200);
            $customers = $query->paginate($perPage);

            return response()->json([
                'data' => $customers->items(),
                'meta' => [
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'last_page' => $customers->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch customers',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created customer
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            // Normalize Malaysian phone number
            $phoneE164 = $this->normalizePhoneNumber($request->phone_raw);
            
            if (!$phoneE164) {
                return response()->json([
                    'error' => [
                        'code' => 'INVALID_PHONE',
                        'message' => 'Invalid Malaysian phone number format',
                        'fields' => ['phone_raw' => ['Please enter a valid Malaysian phone number']]
                    ]
                ], 422);
            }

            // Check for duplicate phone number
            $existing = Customer::where('phone_e164', $phoneE164)->first();
            if ($existing) {
                return response()->json([
                    'error' => [
                        'code' => 'DUPLICATE_PHONE',
                        'message' => 'Customer with this phone number already exists',
                        'existing_customer' => [
                            'id' => $existing->id,
                            'name' => $existing->name,
                            'phone_raw' => $existing->phone_raw,
                        ]
                    ]
                ], 422);
            }

            $customer = Customer::create([
                'name' => $request->name,
                'phone_raw' => $request->phone_raw,
                'phone_e164' => $phoneE164,
                'email' => $request->email,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'state' => $request->state,
                'notes' => $request->notes,
            ]);

            // Add WhatsApp link
            $customerData = $customer->toArray();
            $customerData['whatsapp_link'] = $customer->whatsapp_link;

            return response()->json([
                'message' => 'Customer created successfully',
                'data' => $customerData
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'CREATE_ERROR',
                    'message' => 'Failed to create customer',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        try {
            $customer = Customer::with(['purchases' => function($query) {
                $query->orderBy('purchase_date', 'desc')->limit(10);
            }])->find($id);
            
            if (!$customer) {
                return response()->json([
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Customer not found'
                    ]
                ], 404);
            }

            $customerData = $customer->toArray();
            $customerData['whatsapp_link'] = $customer->whatsapp_link;
            $customerData['full_address'] = $customer->full_address;

            return response()->json(['data' => $customerData]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to fetch customer',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Normalize Malaysian phone number to E.164 format
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Malaysian phone number patterns
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // Format: 0123456789 -> +60123456789
            return '+6' . $phone;
        } elseif (strlen($phone) === 11 && substr($phone, 0, 2) === '60') {
            // Format: 60123456789 -> +60123456789
            return '+' . $phone;
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '+60') {
            // Already in E.164 format
            return $phone;
        }
        
        return null; // Invalid format
    }
}