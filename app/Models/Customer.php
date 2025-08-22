<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Customer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'phone_raw',
        'phone_e164',
        'email',
        'address_line1',
        'address_line2',
        'city',
        'postcode',
        'state',
        'notes',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function purchases()
    {
        return $this->hasMany(CustomerPurchase::class);
    }

    public function tags()
    {
        return $this->belongsToMany(CustomerTag::class, 'customer_tag_pivot', 'customer_id', 'tag_id');
    }

    // Accessors
    public function getWhatsappLinkAttribute()
    {
        $cleanPhone = str_replace('+', '', $this->phone_e164);
        return "https://wa.me/{$cleanPhone}";
    }

    public function getFullAddressAttribute()
    {
        $address = collect([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->postcode,
            $this->state,
        ])->filter()->implode(', ');

        return $address ?: null;
    }

    // Business Logic Methods
    public function getCategoryInDateRange($from, $to)
    {
        $purchaseCount = $this->purchases()
            ->whereBetween('purchase_date', [$from, $to])
            ->count();

        return match (true) {
            $purchaseCount === 0 => 'No Repeat',
            $purchaseCount === 1 => 'Bronze',
            $purchaseCount >= 2 && $purchaseCount <= 4 => 'Silver',
            $purchaseCount >= 5 && $purchaseCount <= 9 => 'Gold',
            $purchaseCount >= 10 => 'Infinity',
        };
    }

    public function getTotalSpentInDateRange($from, $to)
    {
        return $this->purchases()
            ->whereBetween('purchase_date', [$from, $to])
            ->sum('total_amount');
    }

    // Scopes
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByCategory($query, $category, $from, $to)
    {
        // This would need to be implemented with raw SQL for efficiency
        // For now, we'll handle this in the service layer
        return $query;
    }
}
