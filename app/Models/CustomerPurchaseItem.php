<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomerPurchaseItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit',
        'price_each',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'price_each' => 'decimal:2',
    ];

    // Relationships
    public function purchase()
    {
        return $this->belongsTo(CustomerPurchase::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price_each;
    }

    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : 'Product not found';
    }

    public function getProductSkuAttribute()
    {
        return $this->product ? $this->product->sku : null;
    }
}