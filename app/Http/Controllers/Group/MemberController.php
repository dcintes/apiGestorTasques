<?php

namespace App\Http\Controllers\Group;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberCollection;
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
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function list($group_id)
    {
        $this->checkMember($group_id);

        $members = Member::where('group', $group_id)
            ->whereNotNull('user')
            ->get();

        return response()->json(new MemberCollection($members), 200);
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
        $member = Member::where('group', $group_id)
            ->where('user', $member_id)
            ->first();

        $currentMember = $this->checkMember($group_id);

        // Validam que es ell mateix o un administrador
        if (
            !(array)$member &&
            ($member->user == auth()->user()->id
                || !$currentMember->admin)
        ) {
            throw new ApiException("Forbidden", 403);
        }


        DB::beginTransaction();
        try {
            // Ralació usuaru membre
            $member->user = null;
            $member->save();

            // Tasques assignades
            Task::where('assigned', '=', $member->id)
                ->whereNull('completed_date')
                ->update(['assigned' => null]);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
        }

        return 200;
    }
}
