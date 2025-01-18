 <?php

 use App\Http\Controllers\AdminsUserController;
 use App\Http\Controllers\AuthController;
 use App\Http\Controllers\ExportController;
 use App\Http\Controllers\FileController;
 use App\Http\Controllers\GroupController;
 use App\Http\Controllers\UserGroupController;
 use App\Http\Controllers\UsersUserController;
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
 Route::middleware(['loggingAspect'])->group(function () {
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
 });


// *** ROUTES THAT NEED A TOKEN ***

Route::middleware(['auth:sanctum','loggingAspect'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // GROUPS AND FILES
    Route::post('/groups/createGroup', [GroupController::class, 'createGroup']);
});


//  // *** USERS ROUTES ***

    Route::middleware(['auth:sanctum','AuthAspect:user','loggingAspect'])->group(function () {

    //  MEMBERS
        // OUTSIDE GROUPS ROUTES
        Route::post('/groups/{group_id}/acceptInvitation', [GroupController::class, 'acceptInvitation']);
        Route::post('/groups/{group_id}/rejectInvitation', [GroupController::class, 'rejectInvitation']);
        Route::get('/groups/showInvitations', [GroupController::class, 'showInvitations']);
        Route::get('/groups/showMyGroups', [UserGroupController::class, 'showMyGroups']);
        Route::get('/groups/showOneGroup/{group_id}', [UserGroupController::class, 'showOneGroup']);


        // INSIDE GROUPS ROUTS
        Route::middleware(['AuthAspect:userGroup'])->group(function () {
        Route::post('/groups/{group_id}/addFile', [FileController::class, 'addFile']);
        Route::get('/groups/{group_id}/showApprovedFiles', [FileController::class, 'showApprovedFiles']);
        Route::post('/groups/{group_id}/checkIn', [FileController::class, 'checkIn']);
        Route::post('/groups/{group_id}/checkOut', [FileController::class, 'checkOut']);
        Route::get('/groups/{group_id}/viewVersions/{fileId}', [FileController::class, 'viewVersions']);
        Route::get('/groups/showMyFiles/{group_id}', [UserGroupController::class, 'showMyFiles']);
        });

    // ADMIN
        Route::middleware(['AuthAspect:adminGroup'])->group(function () {
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

    Route::middleware(['auth:sanctum','AuthAspect:admin','loggingAspect'])->group(function () {

        Route::get('admins/showAllUsers', [AdminsUserController::class, 'showAllUsers']);
        Route::get('admins/showAllGroups', [AdminsUserController::class, 'showAllGroups']);
        Route::get('admins/showArchive', [AdminsUserController::class, 'showArchive']);
        Route::get('admins/export-updates', [ExportController::class, 'exportUpdates']);
        Route::get('admins/showUpdates', [AdminsUserController::class, 'showUpdates']);


    });


