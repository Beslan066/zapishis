<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    protected $fillable = [
        'employee_id',
        'day_of_week',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'is_working_day',
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkingHoursInMinutes(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        $minutes = $start->diffInMinutes($end);

        if ($this->break_start && $this->break_end) {
            $breakStart = \Carbon\Carbon::parse($this->break_start);
            $breakEnd = \Carbon\Carbon::parse($this->break_end);
            $minutes -= $breakStart->diffInMinutes($breakEnd);
        }

        return $minutes;
    }
}
