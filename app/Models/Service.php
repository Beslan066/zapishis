<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'price',
        'discount_price',
        'duration_minutes',
        'buffer_before_minutes',
        'buffer_after_minutes',
        'color',
        'icon',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    // Relations
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_service')
            ->withPivot('price', 'duration_minutes')
            ->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFinalPrice(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDurationInHours(): float
    {
        return $this->duration_minutes / 60;
    }
}
