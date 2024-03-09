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

            $query = CommunityCouncil::with(['parish.municipality.state'])
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

            $council = CommunityCouncil::createCommunityCouncil($request);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'Community Council' => CommunityCouncil::find($council->id)
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

            $council = CommunityCouncil::find($id);

            if (!$council)
                return response()->json([
                    'sucess' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'Community Council' => $council
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

            $council = CommunityCouncil::find($id);
        
            if (!$council)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            $council = $council->updateCommunityCouncil($request, $council);

            return response()->json([
                'success' => true,
                'data' => [
                    'tag' => CommunityCouncil::find($council->id)
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

            $council = CommunityCouncil::find($id);
        
            if (!$council)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            $council->deleteCommunityCouncil($id);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'Community Council' => $council
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
