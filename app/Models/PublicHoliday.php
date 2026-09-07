<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
     protected $fillable = [
        'date',
        'observed_date',
        'name',
        'country',
        'state',
        'is_recurring',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'observed_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Auto-calculate observed date before saving
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($holiday) {
            if (empty($holiday->observed_date)) {
                $holiday->observed_date = $holiday->calculateObservedDate();
            }
        });

        static::updating(function ($holiday) {
            if ($holiday->isDirty('date') && empty($holiday->observed_date)) {
                $holiday->observed_date = $holiday->calculateObservedDate();
            }
        });
    }

    public function calculateObservedDate()
    {
        // If state is null (national holiday), we'll calculate later
        // based on company's state
        if ($this->state === null) {
            return $this->date;
        }

        $calculator = new \App\Services\HolidayCalculator($this->state);
        return $calculator->calculateObservedDate($this->date);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeForState($query, $state)
    {
        return $query->where(function($q) use ($state) {
            $q->whereNull('state')
              ->orWhere('state', $state);
        });
    }
}
