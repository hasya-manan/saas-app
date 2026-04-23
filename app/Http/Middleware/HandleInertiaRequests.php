<?php

namespace App\Http\Middleware;

use App\Models\GlobalLookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
            'success' => fn () => $request->session()->get('success'),
            'error'   => fn () => $request->session()->get('error'),
            'message' => fn () => $request->session()->get('message'),
        ],
        
        //Note:: remeber forever for performance because If your global_lookups table grows very large 
        //      (hundreds of rows), sharing it in the middleware might slow down your app slightly 
        //      because the middleware will have to fetch the data from the database on every request.
         
        // Add your global lookups here
            'lookups' => [
            'relationships' => Cache::rememberForever('relationship', function () {
                return GlobalLookup::where('category', 'relationship')
                    ->orderBy('sort_order')
                    ->get(['key', 'label']);
            }),
            
            'genders' => Cache::rememberForever('gender', function () {
                return GlobalLookup::where('category', 'gender')
                    ->orderBy('sort_order')
                    ->get(['key', 'label']);
            }),
        ],
        ];
    }
}
