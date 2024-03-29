<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Testing\TestingController;

use App\Http\Controllers\Auth\{
    AuthController,
    PasswordResetController
};

use App\Http\Controllers\{
    CityController,
    CountryController,
    MunicipalityController,
    ParishController,
    StateController,
    UsersController,
    RoleController,
    PermissionController,
    UserMenuController,
    GenderController,
    ProxyController,
    CommunityCouncilController,
    CircuitController,
    MigrantController,
    VolunteerController,
    ReportController,
    DashboardController,
    ThemeController,
    MiscellaneousController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => 'auth',
    'middleware' => 'cors'
], function () {
    Route::post('login', [AuthController::class , 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('find/{token}', [AuthController::class, 'find'])->name('find');
    Route::post('completed', [AuthController::class, 'completed'])->name('completed');
    Route::post('forgot-password', [PasswordResetController::class, 'forgot_password'])->name('forgot.password');
    Route::get('password/find/{token}', [PasswordResetController::class, 'find'])->name("find");
    Route::post('change', [PasswordResetController::class, 'change'])->name("change");

    Route::middleware('jwt')->group(function () {
        Route::post('2fa/validate', [AuthController::class, 'validate_double_factor_auth'])->name('2fa.validate');
        Route::post('logout', [AuthController::class , 'logout'])->name('logout');
        Route::post('me', [AuthController::class , 'me'])->name('me');
        Route::get('generateQR', [AuthController::class , 'generateQR'])->name('generateQR');
    });
});

//Private Endpoints
Route::group(['middleware' => ['cors','jwt'] ], function(){
     
    //Resources 
    Route::apiResource('users', UsersController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('community-councils', CommunityCouncilController::class);
    Route::apiResource('migrants', MigrantController::class);

    //Users
    Route::group(['prefix' => 'users'], function () {
        Route::get('user/online', [UsersController::class, 'getOnline']);
        Route::post('update/password/{id}', [UsersController::class, 'updatePasswordUser']);
        Route::post('update/password', [UsersController::class, 'updatePassword']);
        Route::post('update/profile',  [UsersController::class, 'updateProfile']);
        Route::post('update/store', [UsersController::class, 'updateStore']);
    });

    //Roles
    Route::group(['prefix' => 'roles'], function () {
        Route::get('role/all', [RoleController::class, 'all']);
    });

    //Permissions
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('permission/all', [PermissionController::class, 'all']);
    });

    //Menu
    Route::group(['prefix' => 'menu'], function () {
        Route::get('/', [UserMenuController::class, 'index']);
        Route::post('/add', [UserMenuController::class, 'store']);
        Route::post('/update', [UserMenuController::class, 'update']);
    });

    //Profile
    Route::group(['prefix' => 'profile'], function () {
       Route::post('/', [ClientController::class, 'profile']);
       Route::post('/changeAvatar', [ClientController::class, 'changeAvatar']);
       Route::post('/changePassword', [ClientController::class, 'changePassword']);
       Route::post('/changePhone', [ClientController::class, 'changePhone']);       
    });

    //Reports
    Route::get('/reports',[ReportController::class, 'reports']);

    //Dashboard Statistics
    Route::get('/dashboard-statistics',[DashboardController::class, 'index']);

    //Migrants
    Route::group(['prefix' => 'migrants'], function () {
        Route::post('delete', [MigrantController::class, 'delete']);
    });
});

//Public Endpoints
Route::apiResource('cities', CityController::class);
Route::apiResource('countries', CountryController::class);
Route::apiResource('genders', GenderController::class);
Route::apiResource('municipalities', MunicipalityController::class);
Route::apiResource('parishes', ParishController::class);
Route::apiResource('states', StateController::class);
Route::apiResource('volunteers', VolunteerController::class);
Route::apiResource('themes', ThemeController::class);
Route::apiResource('circuits', CircuitController::class);

//MISCELLANEOUS
Route::get('miscellaneous/data', [MiscellaneousController::class, 'allData']);
Route::get('miscellaneous/dataMigrant', [MiscellaneousController::class, 'dataMigrant']);

//Volunteers
Route::group(['prefix' => 'volunteers'], function () {
    Route::post('register', [VolunteerController::class, 'register']);
});

//PROXY
Route::get('/proxy-image',[ProxyController::class, 'getImage']);
//Testing Endpoints
Route::get('testing', [TestingController::class , 'permissions'])->name('permissions');
Route::get('emails', [TestingController::class , 'emails'])->name('emails');