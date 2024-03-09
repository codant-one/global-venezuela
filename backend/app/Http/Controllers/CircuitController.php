<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Circuit;
use App\Models\Parish;


class CircuitController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

            $query = Circuit::with(['parish.municipality.state'])
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'role'
                            ])
                        );

            $count = $query->applyFilters(
                                $request->only([
                                    'search',
                                    'orderByField',
                                    'orderBy'
                                ])
                            )->count();

            $circuits = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'circuitsCouncils' => $circuits,
                    'circuitsCouncilsTotalCount' => $count
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
                    'circuit' => Circuit::find($circuits->id)
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

            $circuit = Circuit::find($id);

            if (!$circuit)
                return response()->json([
                    'sucess' => false,
                    'feedback' => 'not_found',
                    'message' => 'Circuito no encontrado'
                ], 404);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'circuit' => $circuit
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

            $circuit = Circuit::find($id);
        
            if (!$circuit)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Circuito no encontrado'
                ], 404);

            $circuit = $circuit->updateCircuit($request, $council);

            return response()->json([
                'success' => true,
                'data' => [
                    'circuit' => Circuit::find($circuit->id)
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
