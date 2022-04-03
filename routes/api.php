<?php

use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\MemberController;
use App\Http\Controllers\User\UserAuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Invitation\InvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
  //User
  Route::get('/user/{id}', [UserController::class, 'show']);
  Route::put('/user/{id}', [UserController::class, 'update']);
  Route::delete('/user/{id}', [UserController::class, 'destroy']);
  Route::get('/user/{id}/groups', [UserController::class, 'groups']);
  Route::get('/user/{id}/invitations', [UserController::class, 'invitations']);

  // Invitations
  Route::post('/invitation', [InvitationController::class, 'create']);
  Route::delete('/invitation/{id}', [InvitationController::class, 'destroy']);
  Route::post('/invitation/{id}/accept', [InvitationController::class, 'accept']);

  // Group
  Route::post('/group', [GroupController::class, 'create']);
  Route::get('/group/{id}', [GroupController::class, 'show']);
  Route::put('/group/{id}', [GroupController::class, 'update']);
  Route::delete('/group/{id}', [GroupController::class, 'destroy']);
  Route::get('/group/{id}/stadistics', [GroupController::class, 'stadistics']);

  // Members
  Route::get('/group/{id}/members', [MemberController::class, 'list']);
  Route::post('/group/{group_id}/member/{member_id}/exit', [MemberController::class, 'exit']);
});
