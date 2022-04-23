<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\StadisticsResource;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\Reward;
use App\Models\Task;
use App\Models\Template_reward;
use App\Models\Template_task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{

    /**
     * Retorna una invitació
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $this->validate($request->all(), [
            'name' => 'required|max:50',
            'description' => 'required|max:255',
            'coin' => 'required|max:50',
        ]);

        try {
            DB::beginTransaction();

            $group = Group::create($data);

            $member = new Member([
                'group_id' => $group->id,
                'user_id' => auth()->user()->id,
                'admin' => true,
            ]);
            $member->balance = 0;
            $member->save();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return response()->json(new GroupResource($group), 201);
    }

    /**
     * Retorna un grup
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function show($group_id)
    {
        $this->checkMember($group_id);

        $group = Group::findOrFail($group_id);

        return response()->json(new GroupResource($group), 200);
    }

    /**
     * Actualitza un grup
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $group_id)
    {
        $this->checkAdmin($group_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|max:50',
            'description' => 'required|max:255',
            'coin' => 'required|max:50',
        ]);

        $group = Group::findOrFail($group_id);
        $group->update($data);

        return response()->json(new GroupResource($group), 200);
    }

    /**
     * Elimina un grup
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id)
    {
        $this->checkAdmin($group_id);

        DB::beginTransaction();
        try {
            // invitations
            Invitation::where('group_id', '=', $group_id)->delete();
            // template_tasks
            Template_task::where('group_id', '=', $group_id)->delete();
            // template_rewards
            Template_reward::where('group_id', '=', $group_id)->delete();
            // tasks
            Task::where('group_id', '=', $group_id)->delete();
            //rewards
            Reward::where('group_id', '=', $group_id)->delete();
            // members
            Member::where('group_id', '=', $group_id)->delete();
            // Eliminam grup
            Group::findOrFail($group_id)->delete();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return 204;
    }

    public function stadistics($group_id)
    {
        $this->checkMember($group_id);

        $stadistics = (object)[
            'tasksByUser' => DB::table('tasks')
                ->select('assigned_id as member_id', DB::raw('count(*) as total'))
                ->where('group_id', '=', $group_id)
                ->groupBy('assigned_id')
                ->get(),
        ];

        return response()->json(new StadisticsResource($stadistics), 200);
    }
}
