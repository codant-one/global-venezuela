<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\VolunteerRequest;
use App\Models\Volunteer;

class VolunteerController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Volunteer::with(['theme', 'state', 'municipality.state', 'circuit.municipality.state'])
                        ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'isState',
                                'state_id',
                                'theme_id',
                                'isMunicipality',
                                'municipality_id',
                                'isCircuit',
                                'circuit_id'
                            ])
                        );

         $count = $query->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy',
                                'isState',
                                'state_id',
                                'theme_id',
                                'isMunicipality',
                                'municipality_id',
                                'isCircuit',
                                'circuit_id'
                            ])
                        )->count();

        $volunteers = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
                'volunteers' => $volunteers,
                'volunteersTotalCount' => $count
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

    public function register(VolunteerRequest $request): JsonResponse
    {
        try {

            Volunteer::registerVolunteer($request);

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

    private function sendMail($id, $info ){

        $user = User::find($id);
        $response = [];

        $data = [
            'title' => $info['title']?? null,
            'user' => $user->name . ' ' . $user->last_name,
            'text' => $info['text'] ?? null,
            'username' => $info['username'] ?? null,
            'password' => $info['password'] ?? null,
            'buttonLink' =>  $info['buttonLink'] ?? null,
            'buttonText' =>  $info['buttonText'] ?? null
        ];

        $email = $user->email;
        $subject = $info['subject'];
        
        try {
            \Mail::send($info['email'], $data, function ($message) use ($email, $subject) {
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $message->to($email)->subject($subject);
            });

            $response['success'] = true;
            $response['message'] = "Tu solicitud se ha procesado satisfactoriamente.";
        } catch (\Exception $e){
            $response['success'] = false;
            $response['message'] = "Ocurrió un error, no se pudo enviar el correo electrónico. ".$e;
        }        

        return $response;

    } 


}
