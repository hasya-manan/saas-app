<?php

namespace App\Services;

use App\Models\PublicHoliday;
use App\Models\CompanyHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
class HolidayService
{
    /**
     * Get all holidays for a company (public + company-specific)
     */
    public function getHolidaysForCompany($tenantId, $year, $companyState)
    {
        $cacheKey = "company_holidays_{$tenantId}_{$year}";

        return Cache::remember($cacheKey, 3600, function() use ($tenantId, $year, $companyState) {
            $calculator = new HolidayCalculator($companyState);

            // Get public holidays
            $publicHolidays = PublicHoliday::active()
                ->where('country', 'Malaysia')
                ->forYear($year)
                ->forState($companyState)
                ->get();

            // Get company holidays
            $companyHolidays = CompanyHoliday::active()
                ->where('tenant_id', $tenantId)
                ->forYear($year)
                ->get();

            $allHolidays = collect();

            // Process public holidays
            foreach ($publicHolidays as $holiday) {
                $observedDate = $holiday->observed_date
                    ? Carbon::parse($holiday->observed_date)
                    : $calculator->calculateObservedDate($holiday->date);

                // For national holidays, recalculate based on company state
                if ($holiday->state === null && $companyState) {
                    $observedDate = $calculator->calculateObservedDate($holiday->date);
                }

                $dateKey = $observedDate->format('Y-m-d');

                $allHolidays->put($dateKey, [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'observed_date' => $observedDate->format('Y-m-d'),
                    'name' => $holiday->name,
                    'type' => 'public',
                    'state' => $holiday->state,
                    'is_substituted' => $holiday->date->format('Y-m-d') !== $observedDate->format('Y-m-d'),
                    'original_day' => Carbon::parse($holiday->date)->format('l'),
                    'observed_day' => $observedDate->format('l'),
                    'is_weekend' => $calculator->isWeekend($observedDate),
                    'editable' => false,
                ]);
            }

            // Process company holidays (override public if same date)
            foreach ($companyHolidays as $holiday) {
                $dateKey = $holiday->date->format('Y-m-d');

                $allHolidays->put($dateKey, [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'observed_date' => $holiday->date->format('Y-m-d'),
                    'name' => $holiday->name,
                    'type' => 'company',
                    'state' => $companyState,
                    'is_substituted' => false,
                    'original_day' => Carbon::parse($holiday->date)->format('l'),
                    'observed_day' => Carbon::parse($holiday->date)->format('l'),
                    'is_weekend' => $calculator->isWeekend($holiday->date),
                    'editable' => true,
                ]);
            }

            return [
                'holidays' => $allHolidays->sortKeys(),
                'config' => [
                    'weekend_days' => $calculator->getWeekendDays(),
                    'primary_rest_day' => $calculator->getPrimaryRestDay(),
                    'rollover_rule' => $calculator->getRolloverRule(),
                ],
            ];
        });
    }

    /**
     * Check if a specific date is a holiday
     */
    public function isHoliday($date, $tenantId, $companyState)
    {
        $dateKey = Carbon::parse($date)->format('Y-m-d');

        // Check public holidays
        $publicHoliday = PublicHoliday::active()
            ->where('country', 'Malaysia')
            ->forState($companyState)
            ->where(function($query) use ($dateKey) {
                $query->where('date', $dateKey)
                      ->orWhere('observed_date', $dateKey);
            })
            ->exists();

        if ($publicHoliday) {
            return true;
        }

        // Check company holidays
        $companyHoliday = CompanyHoliday::active()
            ->where('tenant_id', $tenantId)
            ->where('date', $dateKey)
            ->exists();

        return $companyHoliday;
    }

    /**
     * Calculate working days between two dates
     */
    public function calculateWorkingDays($start, $end, $tenantId, $companyState)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $calculator = new HolidayCalculator($companyState);

        // Get all holidays for the period
        $holidays = $this->getHolidaysForCompany($tenantId, $start->year, $companyState);
        $holidayDates = collect($holidays['holidays'])->keys()->toArray();

        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateKey = $current->format('Y-m-d');

            // Check if it's a working day
            $isWeekend = $calculator->isWeekend($current);
            $isHoliday = in_array($dateKey, $holidayDates);

            if (!$isWeekend && !$isHoliday) {
                $workingDays++;
            }

            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Clear cache for a company
     */
    public function clearCompanyCache($tenantId, $year = null)
    {
        if ($year) {
            Cache::forget("company_holidays_{$tenantId}_{$year}");
        } else {
            // Clear all years
            for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++) {
                Cache::forget("company_holidays_{$tenantId}_{$y}");
            }
        }
    }
}