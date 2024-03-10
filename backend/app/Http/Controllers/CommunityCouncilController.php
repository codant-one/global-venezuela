<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\CommunityCouncil;
use App\Models\Parish;

class CommunityCouncilController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = CommunityCouncil::with(['circuit.parish.municipality.state', 'circuit.city'])
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'state_id'
                            ])
                        );

        $count = $query->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'state_id'
                            ])
                        )->count();

        $communityCouncils = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
                'communityCouncils' => $communityCouncils,
                'communityCouncilsTotalCount' => $count
            ]
        ], 200);
    }



    public function store(Request $request): JsonResponse
    {
        try {

            $communityCouncil = CommunityCouncil::createCommunityCouncil($request);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'communityCouncil' => CommunityCouncil::with(['circuit.parish.municipality.state', 'circuit.city'])->find($communityCouncil->id)
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

    public function show($id)
    {
        try {

            $communityCouncil = CommunityCouncil::with(['inmigrants'])->find($id);

            if (!$communityCouncil)
                return response()->json([
                    'sucess' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'communityCouncil' => $communityCouncil
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

            $communityCouncil = CommunityCouncil::find($id);
        
            if (!$communityCouncil)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            $communityCouncil = $communityCouncil->updateCommunityCouncil($request, $communityCouncil);

            return response()->json([
                'success' => true,
                'data' => [
                    'communityCouncil' => CommunityCouncil::with(['circuit.parish.municipality.state', 'circuit.city'])->find($communityCouncil->id)
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

    public function destroy($id): JsonResponse
    {
        try {

            $communityCouncil = CommunityCouncil::find($id);
        
            if (!$communityCouncil)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            $communityCouncil->deleteCommunityCouncil($id);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'Community Council' => $communityCouncil
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
