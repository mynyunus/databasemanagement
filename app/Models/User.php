<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // Remove the 'hashed' cast - it's not available in Laravel 11
    ];

    // Relationships
    public function productGroups()
    {
        return $this->hasMany(ProductGroup::class, 'created_by');
    }

    public function csvUploads()
    {
        return $this->hasMany(CsvUpload::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}