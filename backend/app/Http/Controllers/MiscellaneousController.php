<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\State;
use App\Models\Theme;
use App\Models\Municipality;
use App\Models\Parish;
use App\Models\Circuit;
use App\Models\City;
use App\Models\Gender;
use App\Models\CommunityCouncil;
use App\Models\Country;

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
        $cities = City::all();

        return response()->json([
            'success' => true,
            'data' => [
                'themes' => $themes,
                'states' => $states,
                'municipalities' => $municipalities,
                'parishes' => $parishes,
                'circuits' => $circuits,
                'cities' => $cities
            ]
        ], 200);
    }

    public function dataMigrant(Request $request)
    {
        $themes = Theme::all();
        $states = State::all();
        $municipalities = Municipality::all();
        $parishes = Parish::all();
        $circuits = Circuit::all();
        $cities = City::all();
        $genders = Gender::all();
        $communityCouncils = CommunityCouncil::all();
        $countries = Country::all();

        return response()->json([
            'success' => true,
            'data' => [
                'themes' => $themes,
                'states' => $states,
                'municipalities' => $municipalities,
                'parishes' => $parishes,
                'circuits' => $circuits,
                'cities' => $cities,
                'genders' => $genders,
                'communityCouncils' => $communityCouncils,
                'countries' => $countries
            ]
        ], 200);
    }
}
