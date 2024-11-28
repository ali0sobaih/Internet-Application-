 <?php

use App\Http\Controllers\AuthController;
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

Route::post('/registeration', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// *** ROUTES THAT NEED A TOKEN ***

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
});



//  // *** USERS ROUTES ***

    Route::middleware(['auth:sanctum','user'])->group(function () {

        
    });



//  // *** ADMIN ROUTES ***

    Route::middleware(['auth:sanctum','admin'])->group(function () {

            
    });


