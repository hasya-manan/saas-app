<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateWeekendConfig extends Model
{
    protected $fillable = [
        'state',
        'country',
        'weekend_days',
        'primary_rest_day',
        'secondary_rest_day',
        'rollover_primary',
        'rollover_secondary',
        'rollover_target_primary',
        'rollover_target_secondary',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'weekend_days' => 'array',
        'rollover_primary' => 'boolean',
        'rollover_secondary' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Get readable weekend days
    public function getWeekendDaysTextAttribute()
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return array_map(fn($day) => $days[$day] ?? 'Unknown', $this->weekend_days);
    }

    // Get rollover rule text
    public function getRolloverRuleAttribute()
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $primary = $days[$this->primary_rest_day] ?? 'Unknown';
        $target = $days[$this->rollover_target_primary] ?? 'Unknown';

        if ($this->rollover_primary) {
            return "Holiday on {$primary} → Observed on {$target}";
        }

        return "Holiday on {$primary} → No rollover";
    }

    // Scope for active configs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
