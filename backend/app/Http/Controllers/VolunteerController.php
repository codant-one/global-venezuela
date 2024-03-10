<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Volunteer;

class VolunteerController extends Controller
{
    

    public function store(Request $request): JsonResponse
    {
        try {

            $volunteer = Volunteer::createVolunteer($request);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'volunteer' => Volunteer::find($volunteer->id)
                ]
            ]);

        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'database_error',
                'exception' => $ex->getMessage()
            ], 500);
        }
    }
}
