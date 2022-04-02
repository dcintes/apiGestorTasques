<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupCollection;
use App\Http\Resources\InvitationCollection;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    /**
     * Retorna un usuari
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->isLogedUser($id);

        $user = User::findOrFail($id);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * Actualitza un usuari
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->isLogedUser($id);

        $data = $this->validate($request->all(), [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)
            ],
        ]);

        $user = User::findOrFail($id);
        $user->update($data);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * Elimina un usuari
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->isLogedUser($id);

        User::findOrFail($id)->delete();
        //FIXME: revisar cascade

        return 204;
    }

    /**
     * Llista els grups d'un usuari
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function groups($id)
    {
        $this->isLogedUser($id);

        $groups = Group::join('members', 'groups.id', '=', 'members.group')
            ->where('members.user', $id)
            ->get();

        return response()->json(new GroupCollection($groups), 200);
    }

    /**
     * Llista les invitacions d'un usuari.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function invitations($id)
    {
        $this->isLogedUser($id);

        $user = User::findOrFail($id);

        $invitations = Invitation::where('email', $user->email)
            ->get();

        return response()->json(new InvitationCollection($invitations), 200);
    }
}
