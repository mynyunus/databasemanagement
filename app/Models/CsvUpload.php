<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CsvUpload extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'original_filename',
        'row_count',
        'status',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getIsProcessedAttribute()
    {
        return $this->status === 'processed';
    }

    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}