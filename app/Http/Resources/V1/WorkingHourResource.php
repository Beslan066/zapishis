<?php


namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHourResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'day_label' => $this->getDayLabel(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'break_start' => $this->break_start,
            'break_end' => $this->break_end,
            'is_working_day' => $this->is_working_day,
            'working_hours_minutes' => $this->getWorkingHoursInMinutes(),
        ];
    }

    protected function getDayLabel(): string
    {
        $days = [
            'monday' => 'Понедельник',
            'tuesday' => 'Вторник',
            'wednesday' => 'Среда',
            'thursday' => 'Четверг',
            'friday' => 'Пятница',
            'saturday' => 'Суббота',
            'sunday' => 'Воскресенье',
        ];
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }
}
