<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Migrant;

class DashboardController extends Controller
{
    public function index()
    {
        $total_migrant =  Migrant::count();

        $migrant_by_user = Migrant::where('user_id', auth()->user()->id) ->count();

        $undocumented = Migrant::where('user_id', auth()->user()->id)
                                 ->where('transient', 0)
                                 ->where('resident',0)
                                 ->count();
    
        $parish = Migrant::whereHas('parish.municipality.state')->pluck('id');
        
        $states = count($parish);

        $resident = Migrant::where('resident', 1) ->count();
        $transient = Migrant::where('transient', 1) ->count();
        $isMarried = Migrant::where('isMarried', 1) ->count();
        $has_children = Migrant::where('has_children', 1) ->count();

        $topcountries = Migrant::withCount('country')
        ->orderByDesc('country_count')
        ->take(5)
        ->get();

        $response = [
            'total_migrant' => $total_migrant,
            'migrant_by_user' => $migrant_by_user, 
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
