<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Volunteer;

class VolunteerController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Volunteer::with(['theme', 'state', 'municipality', 'circuit'])
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy'
                            ])
                        );

         $count = $query->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy'
                            ])
                        )->count();

        $volunteers = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
                'Volunteers' => $volunteers,
                'VolunteersTotalCount' => $count
            ]
        ], 200);
    }

    

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

    public function update(Request $request, $id): JsonResponse
    {
        try {

            $volunteer = Volunteer::find($id);
        
            if (!$volunteer)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Voluntario no encontrado'
                ], 404);

            $volunteer = $volunteer->updateVolunteer($request, $volunteer);

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

    public function show(Request $request, $id)
    {
        try {

            $volunteer = Volunteer::with(['theme', 'state', 'municipality', 'circuit'])->find($id);
        
            if (!$volunteer)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Voluntario no encontrado'
                ], 404);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'volunteer' => $volunteer
                ]
            ], 200);

        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'database_error',
                'exception' => $ex->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {

            $volunteer = Volunteer::find($id);
        
            if (!$volunteer)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Voluntario no encontrado'
                ], 404);

            $volunteer->deleteVolunteer($id);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'volunteer' => $volunteer
                ]
            ], 200);
            
        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'database_error',
                'exception' => $ex->getMessage()
            ], 500);
        }
    
    }


}
