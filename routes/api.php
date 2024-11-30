 <?php

use App\Http\Controllers\AuthController;
 use App\Http\Controllers\GroupController;
 use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|-------------------------------------------------------------------------|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// *** ROUTES WITHOUT TOKEN ***

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



// *** ROUTES THAT NEED A TOKEN ***

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
});



//  // *** USERS ROUTES ***

    Route::middleware(['auth:sanctum','user'])->group(function () {

        // GROUPS
        // *** GROUP ADMIN ***
        Route::post('/createGroup', [GroupController::class, 'createGroup']); // **** you need to send the name of the group created
        Route::post('/deleteGroup', [GroupController::class, 'deleteGroup']);
        Route::post('/acceptJoinRequest', [GroupController::class, 'acceptJoinRequest']);
        Route::post('/inviteToGroup', [GroupController::class, 'inviteToGroup']); // **** you need to send the user_name of the invited user and the group_id

        // *** GROUP MEMBER or USERS INVITED TO THE GROUP ***
        Route::post('/joinGroup', [GroupController::class, 'joinGroup']);
        Route::post('/acceptInvitation/{id}', [GroupController::class, 'acceptInvitation']);
        Route::get('/showInvitations', [GroupController::class, 'showInvitations']);


    });



//  // *** ADMIN ROUTES ***

    Route::middleware(['auth:sanctum','admin'])->group(function () {


    });


