<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Inmigrant;

class DashboardController extends Controller
{
    public function index()
    {
        $total_inmigrant =  Inmigrant::count();

        $inmigrant_by_user = Inmigrant::where('user_id', auth()->user()->id) ->count();

        $undocumented = Inmigrant::where('user_id', auth()->user()->id)
                                 ->where('transient', 0)
                                 ->where('resident',0)
                                 ->count();
    
        $parish = Inmigrant::whereHas('parish.municipality.state')->pluck('id');
        
        $states = count($parish);

        $resident = Inmigrant::where('resident', 1) ->count();
        $transient = Inmigrant::where('transient', 1) ->count();
        $isMarried = Inmigrant::where('isMarried', 1) ->count();
        $has_children = Inmigrant::where('has_children', 1) ->count();

        $topcountries = Inmigrant::withCount('country')
        ->orderByDesc('country_count')
        ->take(5)
        ->get();

        $response = [
            'total_inmigrant' => $total_inmigrant,
            'inmigrant_by_user' => $inmigrant_by_user, 
            'undocumented' => $undocumented,
            'states' => $states, 
            'resident' => $resident,
            'transient' => $transient, 
            'isMarried' => $isMarried,
            'has_children' => $has_children,
            'topcountries' => $topcountries
        ];
    
        return response()->json($response);

    }
}
