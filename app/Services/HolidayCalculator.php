<?php

namespace App\Services;

use App\Models\StateWeekendConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
class HolidayCalculator
{
    /**
     * Create a new class instance.
     */
    private $config;
    private $state;
    public function __construct($state)
    {
        $this->state = $state;
        $this->config = $this->getConfig($state);
    }

    /**
     * Get state configuration (with caching)
     */
    private function getConfig($state)
    {
        $cacheKey = "state_config_{$state}";

        return Cache::remember($cacheKey, 86400, function() use ($state) {
            $config = StateWeekendConfig::where('state', $state)
                ->where('is_active', true)
                ->first();

            if (!$config) {
                throw new \Exception("State configuration not found for: {$state}");
            }

            return $config;
        });
    }

    /**
     * Calculate observed date based on state rules
     */
  public function calculateObservedDate($date)
    {
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek;

        // Check Primary Rest Day
        if ($dayOfWeek == $this->config->primary_rest_day) {
            if ($this->config->rollover_primary) {
                $targetDay = (int) $this->config->rollover_target_primary; 
                return $this->calculateRollover($date, $dayOfWeek, $targetDay);
            }
            return $date; 
        }

        // Check Secondary Rest Day
        if ($dayOfWeek == $this->config->secondary_rest_day) {
            if ($this->config->rollover_secondary) {
                $targetDay = (int) $this->config->rollover_target_secondary; 
                return $this->calculateRollover($date, $dayOfWeek, $targetDay);
            }
            return $date;
        }

        return $date;
    }

    // Helper function to keep code clean
    private function calculateRollover($date, $dayOfWeek, $targetDay)
    {
        $daysToAdd = ($targetDay - $dayOfWeek + 7) % 7;
        if ($daysToAdd === 0) {
            $daysToAdd = 7;
        }
        return $date->copy()->addDays($daysToAdd);
    }

    /**
     * Check if a date is a weekend
     */
    public function isWeekend($date)
    {
        $date = Carbon::parse($date);
        $weekendDays = $this->config->weekend_days;
        return in_array($date->dayOfWeek, $weekendDays);
    }

    /**
     * Get weekend days for this state
     */
    public function getWeekendDays()
    {
        return $this->config->weekend_days;
    }

    /**
     * Get primary rest day
     */
    public function getPrimaryRestDay()
    {
        return $this->config->primary_rest_day;
    }

    /**
     * Get rollover rule description
     */
    public function getRolloverRule()
    {
        return $this->config->rollover_rule;
    }

    

    /**
     * Clear cache for this state
     */
    public function clearCache()
    {
        Cache::forget("state_config_{$this->state}");
    }
}
