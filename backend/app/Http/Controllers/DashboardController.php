<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Migrant;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Circuit;
use App\Models\Volunteer;
use App\Models\User;

use DB; 

class DashboardController extends Controller
{
    public function index()
    {
        $total_migrant =  Migrant::count();
        $migrant_by_user = Migrant::where('user_id', auth()->user()->id) ->count();
        $undocumented = Migrant::where([['transient', 0],['resident',0]])->count();

        $states = 
            Migrant::query()
                ->join('parishes', 'migrants.parish_id', '=', 'parishes.id')
                ->join('municipalities', 'parishes.municipality_id', '=', 'municipalities.id')
                ->join('states', 'municipalities.state_id', '=', 'states.id')
                ->select('states.id')
                ->distinct()
                ->get()
                ->count();

        $resident = Migrant::where('resident', 1) ->count();
        $transient = Migrant::where('transient', 1) ->count();
        $isMarried = Migrant::where('isMarried', 1) ->count();
        $has_children = Migrant::where('has_children', 1) ->count();

        $completedCircuit = 
            Circuit::withCount('volunteers')
                ->having('volunteers_count', '>=', 7)
                ->get();

        $completedMunicipality = 
            Municipality::withCount('volunteers')
                ->having('volunteers_count', '>=', 7)
                ->get();

        $completedState = 
            State::withCount('volunteers')
                ->having('volunteers_count', '>=', 7)
                ->get();

        $migrantsByCountries = 
            Migrant::join('countries', 'migrants.country_id', '=', 'countries.id')
                ->select('countries.name as name', 'countries.iso as iso', DB::raw('count(*) as total'))
                ->groupBy('countries.name', 'countries.iso')
                ->orderByDesc('total')
                ->get();

        $top6Countries = $migrantsByCountries->take(6);
        $othersTotal = $top6Countries->slice(6)->sum('total');

        if ($othersTotal > 0) {
            $top6Countries->push((object)[
                'name' => 'Otros',
                'total' => $othersTotal,
            ]);
        }

        $countries = $top6Countries->map(function ($country) use ($total_migrant) {
            $percentage = ($country['total'] / $total_migrant) * 100;
            return [
                'name' => $country['name'],
                'total' => $country['total'],
                'percentage' => round($percentage, 2),
                'iso' => $country['iso']
            ];
        });

        $users = 
            User::whereHas('roles', function ($query) {
                $query->where('name', 'Administrador')
                      ->orWhere('name', 'Operador');
            })->count();

        $response = [
            'total_migrant' => $total_migrant,
            'migrant_by_user' => $migrant_by_user, 
            'undocumented' => $undocumented,
            'states' => $states, 
            'resident' => $resident,
            'transient' => $transient, 
            'isMarried' => $isMarried,
            'has_children' => $has_children,
            'stateCount' => State::count(),
            'municipalityCount' => Municipality::count(),
            'circuitCount' => Circuit::count(),
            'completedCircuit' => $completedCircuit->count(),
            'completedMunicipality' => $completedMunicipality->count(),
            'completedState' => $completedState->count(),
            'countries' => $countries,
            'volunteerCount' => Volunteer::count(),
            'userCount' => $users
        ];
    
        return response()->json($response);

    }
}
