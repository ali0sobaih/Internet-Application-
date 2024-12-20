 <?php

use App\Http\Controllers\AuthController;
 use App\Http\Controllers\FileController;
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

    // GROUPS AND FILES
    Route::post('/groups/createGroup', [GroupController::class, 'createGroup']);
});


//  // *** USERS ROUTES ***

    Route::middleware(['auth:sanctum','user'])->group(function () {

    //  MEMBERS
        // OUTSIDE GROUPS ROUTES
        Route::post('/groups/{group_id}/acceptInvitation', [GroupController::class, 'acceptInvitation']);
        Route::post('/groups/{group_id}/rejectInvitation', [GroupController::class, 'rejectInvitation']);
        Route::get('/groups/{group_id}/showInvitations', [GroupController::class, 'showInvitations']);

        // INSIDE GROUPS ROUTS
        Route::middleware(['check_group_member'])->group(function () {
        Route::post('/groups/{group_id}/addFile', [FileController::class, 'addFile']);
        Route::get('/groups/{group_id}/showApprovedFiles', [FileController::class, 'showApprovedFiles']);
        });

    // ADMIN
        Route::middleware(['check_group_admin'])->group(function () {
        // ON GROUPS ROUTES
        Route::post('/groups/{group_id}/deleteGroup', [GroupController::class, 'deleteGroup']);
        Route::post('/groups/{group_id}/inviteToGroup', [GroupController::class, 'inviteToGroup']);
        // ON Files ROUTES
        Route::post('/groups/{group_id}/approveFile/{id}', [FileController::class, 'approveFile']);
        Route::post('/groups/{group_id}/rejectFile/{id}', [FileController::class, 'rejectFile']);
        Route::get('/groups/{group_id}/showPendingFiles', [FileController::class, 'showPendingFiles']);
        });
    });


//  // *** SYSTEM ADMIN ROUTES ***

    Route::middleware(['auth:sanctum','admin'])->group(function () {


    });


