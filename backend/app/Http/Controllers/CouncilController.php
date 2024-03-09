<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CommunityCouncil;
use App\Models\Parish;
use Illuminate\Http\JsonResponse;


class CouncilController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $councils = CommunityCouncil::all();
            return response()->json($councils);
        } catch (\Exception $e) {
            // Manejar la excepción aquí, por ejemplo, registrarla o devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener los consejos comunales: ' . $e->getMessage()], 500);
        }
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
