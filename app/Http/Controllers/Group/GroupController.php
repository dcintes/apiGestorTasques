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
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'coin' => 'required|max:255',
        ]);

        try {
            DB::beginTransaction();

            $group = Group::create($data);

            $member = new Member([
                'group' => $group->id,
                'user' => auth()->user()->id,
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
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->checkMember($id);

        $group = Group::findOrFail($id);

        return response()->json(new GroupResource($group), 200);
    }

    /**
     * Actualitza un grup
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkAdmin($id);

        $data = $this->validate($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'coin' => 'required|max:255',
        ]);

        $group = Group::findOrFail($id);
        $group->update($data);

        return response()->json(new GroupResource($group), 200);
    }

    /**
     * Elimina un grup
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkAdmin($id);

        DB::beginTransaction();
        try {
            // invitations
            Invitation::where('group', '=', $id)->delete();
            // template_tasks
            Template_task::where('group', '=', $id)->delete();
            // template_rewards
            Template_reward::where('group', '=', $id)->delete();
            // tasks
            Task::where('group', '=', $id)->delete();
            //rewards
            Reward::where('group', '=', $id)->delete();
            // members
            Member::where('group', '=', $id)->delete();
            // Eliminam grup
            Group::findOrFail($id)->delete();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
        }

        return 204;
    }

    public function stadistics($id)
    {
        $this->checkMember($id);

        $stadistics = (object)[
            'tasksByUser' => DB::table('tasks')
                ->select('assigned', DB::raw('count(*) as total'))
                ->groupBy('assigned')
                ->get(),
        ];

        return response()->json(new StadisticsResource($stadistics), 200);
    }
}
