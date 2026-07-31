<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'period',
        'period_start',
        'period_end',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function scopePeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }
}
