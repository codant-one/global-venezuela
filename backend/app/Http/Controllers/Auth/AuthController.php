<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\userDetail;
use App\Models\UserRegisterToken;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => 
            ['login', 'register', 'find', 'completed']
        ]);
    }

    /**
     * Store a newly created resource in storage
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'missing_params',
                'errors' => $validator->errors()
            ], 400);
        }

        $credentials = request(['email', 'password']);

        $expired = now()->addHours(24);
            
        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'invalid_credentials',
                'errors' => 'Usuario o contraseña invalida'
            ], 400);
        }

        $user = Auth::user();
        $user->online = Carbon::now();
        $user->save();

        if (env('APP_DEBUG') || ($user->is_2fa === 0)) {
            return response()->json([
                'success' => true,
                'message' => 'login_success',
                'data' => $this->respondWithToken($token)
            ], 200);
        }

        if (empty($user->token_2fa)) {
            $google2fa = app('pragmarx.google2fa');
            $token2FA = $google2fa->generateSecretKey();

            $user->token_2fa = $token2FA;
            $user->update();

            $qr = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $token2FA
            );

            $data = [
                'qr' => $qr,
                'token' => $token2FA
            ];

            return response()->json([
                'success' => true,
                'message' => '2fa-generate',
                'data' => array_merge($data, $this->respondWithToken($token))
            ], 200);

        } else {

            $data = [
                'token' => $user->token_2fa
            ];

            return response()->json([
                'success' => true,
                'message' => '2fa',
                'data' => array_merge($data, $this->respondWithToken($token))
            ], 200);
        }
    }

    public function validate_double_factor_auth(Request $request)
    {
        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($user->token_2fa, $request->token_2fa)) {
            session()->put('2fa', '1');

            if($request->panel) {
                $user->is_2fa =  ($user->is_2fa === 0) ? 1 : 0;
                $user->update();
            }

            return response()->json([
                'success' => true,
                'message' => 'login_success'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'invalid_code',
            'errors' => 'Código de verificación incorrecto'
        ], 400);
    }

    public function generateQR()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $token2FA = '';

        if (empty($user->token_2fa)) {
            $token2FA = $google2fa->generateSecretKey();

            $user->token_2fa = $token2FA;
            $user->update();
        }

        $qr = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            empty($user->token_2fa) ? $token2FA : $user->token_2fa
        );

        $data = [
            'qr' => $qr,
            'is_2fa' => ($user->is_2fa === 0) ? false : true,
            'token' => empty($user->token_2fa) ? $token2FA : $user->token_2fa
        ];

        return response()->json([
            'success' => true,
            'message' => 'generate-qr',
            'data' => $data
        ], 200);

        return response()->json([
            'success' => false,
            'message' => 'invalid_code',
            'errors' => 'Código de verificación incorrecto'
        ], 400);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $hash = $request->hash;

        $user = Auth::user();
        $user->online = Carbon::now();
        $user->save();

        if($user->password === $hash){

            $permissions = getPermissionsByRole(Auth::user());
            $userData = getUserData(Auth::user()->load(['userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender']));

            return response()->json([
                'success' => true,
                'data' => [ 
                    'user_data' => $userData,
                    'userAbilities' => $permissions
                ]
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'params_validation_failed',
                'error' => 'Data does not match'
            ], 400);
        }

    }

    /**
     * Log the user out (Invalidate the token).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Log out successfully'
        ], 200);
    }

    public function find($token)
    {
        
        try {

            $emailConfirm = UserRegisterToken::where('token', $token)->first();

            if (!$emailConfirm)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Token inválido'
                ], 404);

            if (Carbon::parse($emailConfirm->updated_at)->addMinutes(720)->isPast()) {
                $emailConfirm->delete();

                return response()->json([
                    'success' => false,
                    'feedback' => 'error_token',
                    'message' => 'Token vencido'
                ], 404);
                
            }

            return response()->json([
                'success' => true,
                'message' => 'Confirmación de correo electrónico exitosa',
                'data' => [ 
                    'token' => $token
                ]
            ], 200);

        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'database_error '.$ex->getMessage(),
                'exception' => $ex->getMessage()
            ], 500);
        }

    }

    public function completed(Request $request)
    {
        try {
        
            $emailConfirm = UserRegisterToken::where('token', $request->token)->first();
            $user = User::where('id', $emailConfirm->user_id)->first();

            if (!$user)
                return response()->json([
                    'success' => false,
                    'feedback' => 'not_found',
                    'message' => 'Cliente no registrado'
                ], 404);

            if ($user->email_verified_at == null) {
                $user->email_verified_at = now();
                $user->update();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tu solicitud se ha procesado satisfactoriamente. Correo electrónico verificado. Le invitamos a que inicie sesion',
            ], 200);

        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'database_error '.$ex->getMessage(),
                'exception' => $ex->getMessage()
            ], 500);
        }
    } 

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $permissions = getPermissionsByRole(Auth::user());
        $userData = getUserData(Auth::user()->load(['userDetail.parish.municipality.state', 'userDetail.city', 'userDetail.gender']));

        return [
            'accessToken' => $token,
            'token_type' => 'bearer',
            'user_data' => $userData,
            'userAbilities' => $permissions
        ];
    }
}
