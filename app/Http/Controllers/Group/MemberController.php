<?php

namespace App\Http\Controllers\Group;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberCollection;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{

    /**
     * Get all members of a group
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function list($group_id)
    {
        $this->checkMember($group_id);

        $members = Member::where('group_id', $group_id)
            ->whereNotNull('user_id')
            ->get();

        return response()->json(new MemberCollection($members), 200);
    }

    /**
     * Get member
     *
     * @param  $group_id
     * @param  $member_id
     * @return \Illuminate\Http\Response
     */
    public function show($group_id, $member_id)
    {
        $this->checkMember($group_id);

        $member = Member::findOrFail($member_id);

        return response()->json(new MemberResource($member), 200);
    }

    /**
     * Get all members of a group
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function exit($group_id, $member_id)
    {
        // Obtenim el membre a treure
        $member = Member::findOrFail($member_id);

        $currentMember = $this->checkMember($group_id);

        // Validam que es ell mateix o un administrador
        $permisos = ($member->user_id == auth()->user()->id || $currentMember->admin);
        if (
            $member->group_id != $group_id || !$permisos
        ) {
            throw new ApiException("Forbidden", 404);
        }

        DB::beginTransaction();
        try {
            // Ralació usuari membre
            $member->user_id = null;
            $member->save();

            // Tasques assignades
            Task::where('assigned_id', '=', $member->id)
                ->whereNull('completed_date')
                ->update(['assigned_id' => null]);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return 200;
    }
}
