<?php

namespace App\Http\Controllers;

use Spatie\Permission\Middlewares\PermissionMiddleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Inmigrant;

class InmigrantController extends Controller
{

    public function __construct()
    {
        $this->middleware(PermissionMiddleware::class . ':ver inmigrantes|administrador')->only(['index']);
        $this->middleware(PermissionMiddleware::class . ':crear inmigrantes|administrador')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':editar inmigrantes|administrador')->only(['update','updatePasswordUser']);
        $this->middleware(PermissionMiddleware::class . ':eliminar inmigrantes|administrador')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Inmigrant::with(['country', 'user', 'gender', 'community_council', 'parish.municipality.state'])
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

        $inmigrants = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
                'inmigrants' => $inmigrants,
                'inmigrantsTotalCount' => $count
            ]
        ], 200);
    }



    public function store(Request $request): JsonResponse
    {
        try {

            $inmigrant = Inmigrant::createInmigrant($request);

            
            if ($request->hasFile('file_document')) {
                $document = $request->file('file_document');

                $path = 'inmigrants/';

                $file_data = uploadFile($document, $path);

                $inmigrant->file_document = $file_data['filePath'];
                $inmigrant->update();
            } 


            return response()->json([
                'success' => true,
                'data' => [ 
                    'inmigrant' => Inmigrant::with(['parish.municipality.state'])->find($inmigrant->id)
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

            $inmigrant = Inmigrant::with(['country', 'user', 'gender', 'community_council.circuit', 'parish.municipality.state'])->find($id);
        
            if (!$inmigrant)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Inmigrante no encontrado'
                ], 404);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'inmigrant' => $inmigrant
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
    public function update(Request $request, Inmigrant $inmigrant): JsonResponse
    {
        try {

            $inmigrant = Inmigrant::find($inmigrant->id);
        
            if (!$inmigrant)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Inmigrante no encontrado'
                ], 404);

            $inmigrant = $inmigrant->updateInmigrant($request, $inmigrant);

            if ($request->hasFile('file_document')) {
                $document = $request->file('file_document');

                $path = 'inmigrants/';

                $file_data = uploadFile($document, $path, $inmigrant->file_document);

                $inmigrant->file_document = $file_data['filePath'];
                $inmigrant->update();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'inmigrant' => Inmigrant::with(['parish.municipality.state'])->find($inmigrant->id)
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

            $inmigrant = Inmigrant::find($request->ids);
        
            if (!$inmigrant)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Inmigrante no encontrado'
                ], 404);

            Inmigrant::deleteInmigrants($request->ids);

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
