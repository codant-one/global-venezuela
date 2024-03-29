<?php

namespace App\Http\Controllers;

use Spatie\Permission\Middlewares\PermissionMiddleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\CommunityCouncil;
use App\Models\Migrant;

class CommunityCouncilController extends Controller
{

    public function __construct()
    {
        $this->middleware(PermissionMiddleware::class . ':ver consejos-comunales|administrador')->only(['index']);
        $this->middleware(PermissionMiddleware::class . ':crear consejos-comunales|administrador')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':editar consejos-comunales|administrador')->only(['update','updatePasswordUser']);
        $this->middleware(PermissionMiddleware::class . ':eliminar consejos-comunales|administrador')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = CommunityCouncil::with(['circuit.municipality.state', 'circuit.city'])
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'state_id',
                                'municipality_id',
                                'circuit_id'
                            ])
                        );

        $count = $query->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'state_id',
                                'municipality_id',
                                'circuit_id'
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
                    'communityCouncil' => CommunityCouncil::with(['circuit.municipality.state', 'circuit.city'])->find($communityCouncil->id)
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

            $communityCouncil = CommunityCouncil::with(['migrants'])->find($id);

            if (!$communityCouncil)
                return response()->json([
                    'sucess' => false,
                    'feedback' => 'not_found',
                    'message' => 'Consejo Comunal no encontrado'
                ], 404);

            $limit = $request->has('limit') ? $request->limit : 10;

            $query = Migrant::with(['country', 'user', 'gender', 'community_council', 'parish.municipality.state'])
                        ->where('community_council_id', $id)
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy'
                            ])
                        );

            $count = $query->where('user_id', auth()->user()->id)
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy'
                            ])
                        )->count();

            $migrants = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'communityCouncil' => $communityCouncil,
                    'migrants' => $migrants,
                    'migrantsTotalCount' => $count
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
                    'communityCouncil' => CommunityCouncil::with(['circuit.municipality.state', 'circuit.city'])->find($communityCouncil->id)
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
