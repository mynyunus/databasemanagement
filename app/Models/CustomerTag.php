<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomerTag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'color',
    ];

    // Relationships
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_tag_pivot', 'tag_id', 'customer_id');
    }

    // Accessors
    public function getCustomerCountAttribute()
    {
        return $this->customers()->count();
    }

    // Scopes
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
