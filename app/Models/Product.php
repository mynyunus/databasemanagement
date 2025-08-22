<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_group_id',
        'name',
        'sku',
        'is_active',
        'price_default',
        'price_override',
        'cogs',
        'postage_cost',
        'bottle_qty',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_default' => 'decimal:2',
        'price_override' => 'decimal:2',
        'cogs' => 'decimal:2',
        'postage_cost' => 'decimal:2',
        'bottle_qty' => 'integer',
    ];

    // Relationships
    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(CustomerPurchaseItem::class);
    }

    // Accessors
    public function getFinalPriceAttribute()
    {
        return $this->price_override ?? $this->price_default;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGroup($query, $groupId)
    {
        return $query->where('product_group_id', $groupId);
    }
}