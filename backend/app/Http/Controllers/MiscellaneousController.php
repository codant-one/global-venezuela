<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\State;
use App\Models\Theme;
use App\Models\Municipality;
use App\Models\Parish;
use App\Models\Circuit;

class MiscellaneousController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function allData(Request $request)
    {
        $themes = Theme::all();
        $states = State::all();
        $municipalities = Municipality::all();
        $parishes = Parish::all();
        $circuits = Circuit::all();

        return response()->json([
            'success' => true,
            'data' => [
                'themes' => $themes,
                'states' => $states,
                'municipalities' => $municipalities,
                'parishes' => $parishes,
                'circuits' => $circuits
            ]
        ], 200);
    }

}
