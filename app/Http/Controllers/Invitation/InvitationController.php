<?php

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\MemberResource;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    /**
     * Crea una invitació
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $this->validate($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'email' => 'required|email|max:50',
        ]);

        $this->checkAdmin($data['group_id']);

        $invitation = Invitation::create($data);

        return response()->json(new InvitationResource($invitation), 200);
    }

    /**
     * Descarta una invitació
     *
     * @param  $invitation_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($invitation_id)
    {
        $invitation = Invitation::findOrFail($invitation_id);
        $user = User::where('email', $invitation->email)->first();

        $this->isLogedUser($user->id);

        $invitation->delete();

        return 204;
    }

    /**
     * Retorna una invitació
     *
     * @param  $invitation_id
     * @return \Illuminate\Http\Response
     */
    public function accept($invitation_id)
    {
        $invitation = Invitation::findOrFail($invitation_id);
        $user = User::where('email', $invitation->email)->first();

        $this->isLogedUser($user->id);

        $member = new Member([
            'group_id' => $invitation->group_id,
            'user_id' => $user->id,
            'admin' => false,
        ]);
        $member->balance = 0;

        DB::beginTransaction();
        try {
            $member->save();

            $invitation->delete();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return response()->json(new MemberResource($member), 200);
    }
}
