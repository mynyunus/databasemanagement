<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ActivityLog extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false; // Only created_at

    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // Static methods for logging
    public static function log($action, $subject, $meta = [], $userId = null)
    {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'action' => $action,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeBySubjectType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}