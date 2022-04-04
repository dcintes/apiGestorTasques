<?php

use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\MemberController;
use App\Http\Controllers\User\UserAuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Invitation\InvitationController;
use App\Http\Controllers\Task\TaskController;
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

// Autenticat
Route::middleware('auth:api')->group(function () {
  //User
  Route::get('/user/{user_id}', [UserController::class, 'show']);
  Route::put('/user/{user_id}', [UserController::class, 'update']);
  Route::delete('/user/{user_id}', [UserController::class, 'destroy']);
  Route::get('/user/{user_id}/groups', [UserController::class, 'groups']);
  Route::get('/user/{user_id}/invitations', [UserController::class, 'invitations']);

  // Invitations
  Route::post('/invitation', [InvitationController::class, 'create']);
  Route::delete('/invitation/{invitation_id}', [InvitationController::class, 'destroy']);
  Route::post('/invitation/{invitation_id}/accept', [InvitationController::class, 'accept']);

  // Group
  Route::post('/group', [GroupController::class, 'create']);
  Route::get('/group/{group_id}', [GroupController::class, 'show']);
  Route::put('/group/{group_id}', [GroupController::class, 'update']);
  Route::delete('/group/{group_id}', [GroupController::class, 'destroy']);
  Route::get('/group/{group_id}/stadistics', [GroupController::class, 'stadistics']);

  // Members
  Route::get('/group/{group_id}/members', [MemberController::class, 'list']);
  Route::post('/group/{group_id}/member/{member_id}/exit', [MemberController::class, 'exit']);

  // Tasks
  Route::get('/group/{group_id}/tasks', [TaskController::class, 'tasks']);
  Route::post('/group/{group_id}/task', [TaskController::class, 'create']);
  Route::get('/group/{group_id}/task/{task_id}', [TaskController::class, 'show']);
  Route::put('/group/{group_id}/task/{task_id}', [TaskController::class, 'update']);
  Route::delete('/group/{group_id}/task/{task_id}', [TaskController::class, 'destroy']);
  Route::post('/group/{group_id}/task/{task_id}/assign', [TaskController::class, 'assign']);

  // Rewards
  Route::get('/group/{group_id}/rewards', [RewardController::class, 'list']);
  Route::post('/group/{group_id}/reward/{reward_id}', [RewardController::class, 'show']);
});
