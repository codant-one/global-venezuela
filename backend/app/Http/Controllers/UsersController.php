<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UserRequest;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserRegisterToken;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Middlewares\PermissionMiddleware;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
        $this->middleware(PermissionMiddleware::class . ':ver usuarios|administrador')->only(['index']);
        $this->middleware(PermissionMiddleware::class . ':crear usuarios|administrador')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':editar usuarios|administrador')->only(['update','updatePasswordUser']);
        $this->middleware(PermissionMiddleware::class . ':eliminar usuarios|administrador')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            
            $limit = $request->has('limit') ? $request->limit : 10;;

            $query = User::with(['roles', 'userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender'])
                         ->whereHas('roles', function ($query) {
                            $query->where('name', 'Administrador')
                                  ->orWhere('name', 'Operador');
                         })
                         ->applyFilters(
                            $request->only([
                                'search',
                                'orderByField',
                                'orderBy'
                            ])
                        );

            $count = $query->whereHas('roles', function ($query) {
                                $query->where('name', 'Administrador')
                                      ->orWhere('name', 'Operador');
                            })
                           ->applyFilters(
                                $request->only([
                                    'search',
                                    'orderByField',
                                    'orderBy'
                                ])
                            )->count();

            $users = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'users' => $users,
                    'usersTotalCount' => $count
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
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): JsonResponse
    {
        try{
            
            $user = User::createUser($request);
            $user->syncRoles($request->roles);

            //Crear o Actualizar token.
            $registerConfirm = UserRegisterToken::updateOrCreate(
                ['user_id' => $user->id],
                ['token' => Str::random(60)]
            );

            $info = [
                'title' => 'Cuenta creada satisfactoriamente!!!',
                'user' => $user->name . ' ' . $user->last_name,
                'username' => $user->email,
                'email' => 'emails.auth.user_created',
                'password' => $request->password,
                'text' => 'Tu cuenta no está verificada. Confirma tu cuenta con los pasos a seguir para verificarla.',
                'buttonLink' =>  env('APP_DOMAIN').'/register-confirm?token=' . $registerConfirm['token'],
                'buttonText' => 'Confirmar',
                'subject' => 'Bienvenido a VENEZUELA GLOBAL',
            ];
            
            $this->sendMail($user->id, $info); 

            return response()->json([
                'success' => true,
                'data' => [ 
                    'user' => User::with(['roles', 'userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender'])->find($user->id)
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
    public function update(UserRequest $request, $id): JsonResponse
    {
        try {

            $user = User::find($id);
        
            if (!$user)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Usuario no encontrado'
                ], 404);

            $user->updateUser($request, $user); 
            $user->roles()->detach();
            $user->syncRoles($request->roles);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'user' => $user
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
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {

            $user = User::find($id);
        
            if (!$user)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Usuario no encontrado'
                ], 404);
            
            $user->deleteUser($id);
                        
            return response()->json([
                'success' => true,
                'data' => [ 
                    'user' => $user
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(ProfileRequest $request): JsonResponse
    {

        try {

            $user = Auth::user()->load(['userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender']);
            $user->updateProfile($request, $user);

            if ($request->hasFile('image')) {
                $image = $request->file('image');

                $path = 'avatars/';

                $file_data = uploadFile($image, $path, $user->avatar);

                $user->avatar = $file_data['filePath'];
                $user->update();
            } 

            $userData = getUserData($user->load(['userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender']));

            return response()->json([
                'success' => true,
                'data' => [ 
                    'user_data' => $userData
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validate = Validator::make($request->all(), [
            'password' => 'required'
        ]);
    
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'feedback' => 'params_validation_failed',
                'message' => $validate->errors()
            ], 400);
        }

        try {

            $user = Auth::user()->load(['userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender']);
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'data' => [ 
                    'user_data' => getUserData($user)
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePasswordUser(Request $request, string $id): JsonResponse
    {
        $validate = Validator::make($request->all(), [
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'feedback' => 'params_validation_failed',
                'message' => $validate->errors()
            ], 400);
        }

        try {

            $user = User::with(['userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender'])->find($id);
        
            if (!$user)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Usuario no encontrado'
                ], 404);

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'data' => [ 
                    'user_data' => getUserData($user)
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
     *
     */
    public function getOnline(Request $request): JsonResponse
    {
        try{
            
            $users = User::getOnline($request);

            return response()->json([
                'success' => true,
                'data' => [ 
                    'users' => $users
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
