<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomerPurchase extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'purchase_date',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CustomerPurchaseItem::class, 'purchase_id');
    }

    // Business Logic
    public function calculateTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price_each;
        });
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    // Scopes
    public function scopeInDateRange($query, $from, $to)
    {
        return $query->whereBetween('purchase_date', [$from, $to]);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}