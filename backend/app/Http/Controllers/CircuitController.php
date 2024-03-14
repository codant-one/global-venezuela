<?php

namespace App\Http\Controllers;

use Spatie\Permission\Middlewares\PermissionMiddleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Circuit;
use App\Models\CommunityCouncil;

class CircuitController extends Controller
{

    public function __construct()
    {
        // $this->middleware(PermissionMiddleware::class . ':ver circuitos|administrador')->only(['index']);
        $this->middleware(PermissionMiddleware::class . ':crear circuitos|administrador')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':editar circuitos|administrador')->only(['update','updatePasswordUser']);
        $this->middleware(PermissionMiddleware::class . ':eliminar circuitos|administrador')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Circuit::with(['municipality.state', 'city'])
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'state_id',
                                'municipality_id'
                            ])
                        );

         $count = $query->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'state_id',
                                'municipality_id'
                            ])
                        )->count();

        $circuits = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
                'circuits' => $circuits,
                'circuitsTotalCount' => $count
            ]
        ], 200);
    }



    public function store(Request $request): JsonResponse
    {
        try {

            $circuit = Circuit::createCircuit($request);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'circuit' => Circuit::with(['municipality.state', 'city'])->find($circuit->id)
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

            $circuit = Circuit::with(['community_councils'])->find($id);
        
            if (!$circuit)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Circuito no encontrado'
                ], 404);

            $limit = $request->has('limit') ? $request->limit : 10;

            $query = CommunityCouncil::with(['circuit.municipality.state', 'circuit.city'])
                            ->where('circuit_id', $id)
                            ->applyFilters(
                                $request->only([
                                    'search',
                                    'orderByField',
                                    'orderBy',
                                    'state_id'
                                ])
                            );

            $count = $query->where('circuit_id', $id)
                           ->applyFilters(
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
                    'circuit' => $circuit,
                    'communityCouncils' => $communityCouncils,
                    'communityCouncilsTotalCount' => $count
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

            $circuit = Circuit::find($id);
        
            if (!$circuit)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Circuito no encontrado'
                ], 404);

            $circuit = $circuit->updateCircuit($request, $circuit);

            return response()->json([
                'success' => true,
                'data' => [
                    'circuit' => Circuit::with(['municipality.state', 'city'])->find($circuit->id)
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

            $circuit = Circuit::find($id);
        
            if (!$circuit)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Circuito no encontrado'
                ], 404);

            $circuit->deleteCircuit($id);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'circuit' => $circuit
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
