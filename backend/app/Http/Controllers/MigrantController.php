<?php

namespace App\Http\Controllers;

use Spatie\Permission\Middlewares\PermissionMiddleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Migrant;

class MigrantController extends Controller
{

    public function __construct()
    {
        $this->middleware(PermissionMiddleware::class . ':ver migrantes|administrador')->only(['index']);
        $this->middleware(PermissionMiddleware::class . ':crear migrantes|crear operador-migrantes|administrador')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':editar migrantes|administrador')->only(['update','updatePasswordUser']);
        $this->middleware(PermissionMiddleware::class . ':eliminar migrantes|administrador')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Migrant::with(['country', 'user', 'gender', 'community_council', 'parish.municipality.state'])
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

        $migrants = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
                'migrants' => $migrants,
                'migrantsTotalCount' => $count
            ]
        ], 200);
    }



    public function store(Request $request): JsonResponse
    {
        try {

            $migrant = Migrant::createMigrant($request);

            
            if ($request->hasFile('file_document')) {
                $document = $request->file('file_document');

                $path = 'migrants/';

                $file_data = uploadFile($document, $path);

                $migrant->file_document = $file_data['filePath'];
                $migrant->update();
            } 


            return response()->json([
                'success' => true,
                'data' => [ 
                    'migrant' => Migrant::with(['parish.municipality.state'])->find($migrant->id)
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

            $migrant = Migrant::with(['country', 'user', 'gender', 'community_council.circuit', 'parish.municipality.state'])->find($id);
        
            if (!$migrant)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Migrante no encontrado'
                ], 404);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'migrant' => $migrant
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Migrant $migrant): JsonResponse
    {
        try {

            $migrant = Migrant::find($migrant->id);
        
            if (!$migrant)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Migrante no encontrado'
                ], 404);

            $migrant = $migrant->updateMigrant($request, $migrant);

            if ($request->hasFile('file_document')) {
                $document = $request->file('file_document');

                $path = 'migrants/';

                $file_data = uploadFile($document, $path, $migrant->file_document);

                $migrant->file_document = $file_data['filePath'];
                $migrant->update();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'migrant' => Migrant::with(['parish.municipality.state'])->find($migrant->id)
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

    public function delete(Request $request): JsonResponse
    {
        try {

            $migrant = Migrant::find($request->ids);
        
            if (!$migrant)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Migrante no encontrado'
                ], 404);

            Migrant::deleteMigrants($request->ids);

            return response()->json([
                'success' => true
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
