<?php

namespace App\Http\Controllers\User;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroupCollection;
use App\Http\Resources\InvitationCollection;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    /**
     * Retorna un usuari
     *
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        $this->isLogedUser($user_id);

        $user = User::findOrFail($user_id);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * Actualitza un usuari
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {
        $this->isLogedUser($user_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user_id),
                'max:50'
            ],
            'password' => 'required|confirmed|min:8|max:16',
        ]);

        $user = User::findOrFail($user_id);

        $user->password = bcrypt($data['password']);
        $user->update();

        return response()->json(new UserResource($user), 200);
    }

    /**
     * Elimina un usuari
     *
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id)
    {
        $this->isLogedUser($user_id);

        DB::beginTransaction();
        try {

            // Tasques assignades pero no finalitzades
            Task::join('members', 'members.id', '=', 'tasks.assigned_id')
                ->where('members.user_id', '=', $user_id)
                ->whereNull('completed_date')
                ->update(['assigned_id' => null]);

            // Ralació usuaru membre
            Member::where('user_id', '=', $user_id)
                ->update(['user_id' => null]);

            // Esborram usuari
            User::findOrFail($user_id)->delete();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return 204;
    }

    /**
     * Llista els grups d'un usuari
     *
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function groups($user_id)
    {
        $this->isLogedUser($user_id);

        $groups = Group::join('members', 'groups.id', '=', 'members.group_id')
            ->where('members.user_id', $user_id)
            ->get(['groups.*']);

        return response()->json(new GroupCollection($groups), 200);
    }

    /**
     * Llista les invitacions d'un usuari.
     *
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function invitations($user_id)
    {
        $this->isLogedUser($user_id);

        $user = User::findOrFail($user_id);

        $invitations = Invitation::where('email', $user->email)
            ->get();

        return response()->json(new InvitationCollection($invitations), 200);
    }
}
