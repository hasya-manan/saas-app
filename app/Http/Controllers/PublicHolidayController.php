<?php

namespace App\Http\Controllers;

use App\Services\HolidayService;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Fallback for Super Admin who might not have a tenant or company state
        $tenantId = $user->tenant_id ?? 0; 
        
      
        $companyState = $request->get('state') ?? optional($user->company)->state;

      
        if (!$companyState) {
            return response()->json([
                'status' => 'error',
                'message' => 'Company state is not defined. Please specify a state.',
            ], 422);
        } 
        
        $year = $request->get('year', date('Y'));

        $data = $this->holidayService->getHolidaysForCompany($tenantId, $year, $companyState);

        return response()->json([
            'status' => 'success',
            'year' => $year,
            'state' => $companyState,
            'config' => $data['config'],
            'holidays' => $data['holidays']->values(),
        ]);
    }

    public function clearCache(Request $request)
    {
        // Restrict this to admins or super admins if needed, or keep it open for authenticated users
        $tenantId = $request->user()->tenant_id ?? 0;
        $year = $request->get('year');

        $this->holidayService->clearCompanyCache($tenantId, $year);

        return response()->json([
            'status' => 'success',
            'message' => 'Holiday cache cleared successfully.',
        ]);
    }
}