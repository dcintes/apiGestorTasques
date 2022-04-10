<?php

namespace App\Http\Controllers\User;

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
        ]);

        $user = User::findOrFail($user_id);
        $user->update($data);

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
            // Ralació usuaru membre
            $member = Member::where('user_id', '=', $user_id)
                ->update(['user_id' => null]);
            // Tasques assignades pero no finalitzades
            Task::where('assigned_id', '=', $member->id)
                ->whereNull('completed_date')
                ->update(['assigned_id' => null]);
            // Esborram usuari
            User::findOrFail($user_id)->delete();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
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
            ->get();

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
