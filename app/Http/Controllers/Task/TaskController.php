<?php

namespace App\Http\Controllers\Task;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Member;
use App\Models\Task;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Get tasks of a group
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function list($group_id)
    {
        $this->checkMember($group_id);

        $tasks = Task::where('group_id', $group_id)
            ->get();

        return response()->json(new TaskCollection($tasks), 200);
    }

    /**
     * Crea una taska
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $group_id)
    {
        $this->checkMember($group_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'value' => 'required|numeric'
        ]);

        $task = new Task($data);
        $task->group_id = $group_id;

        $task->save();

        return response()->json(new TaskResource($task), 201);
    }

    /**
     * Retorna una taska
     *
     * @param  $group_id
     * @param  $task_id
     * @return \Illuminate\Http\Response
     */
    public function show($group_id, $task_id)
    {
        $this->checkMember($group_id);

        $task = Task::findOrFail($task_id);

        if ($group_id != $task->group_id) {
            throw new ApiException("Aquesta tasca no pertany al grup", 400);
        }

        return response()->json(new TaskResource($task), 200);
    }

    /**
     * Actualitza una task
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $group_id
     * @param  $task_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $group_id, $task_id)
    {
        $this->checkAdmin($group_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'value' => 'required|numeric'
        ]);

        $task = Task::findOrFail($task_id);

        if ($group_id != $task->group_id) {
            throw new ApiException("Aquesta tasca no pertany al grup", 400);
        }

        $task->update($data);

        return response()->json(new TaskResource($task), 200);
    }

    /**
     * Elimina una taska
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id, $task_id)
    {
        $this->checkAdmin($group_id);

        $task = Task::findOrFail($task_id);

        if ($group_id != $task->group_id) {
            throw new ApiException("Aquesta tasca no pertany al grup", 400);
        }

        $task->delete();

        return 204;
    }

    /**
     * Assigna la tasca a un member
     *
     * @param  $group_id
     * @param  $task_id
     * @return void
     */
    public function assign(Request $request, $group_id, $task_id)
    {
        $this->checkMember($group_id);

        $data = $this->validate($request->all(), [
            'member_id' => 'required|numeric'
        ]);

        $task = Task::findOrFail($task_id);

        if ($group_id != $task->group_id) {
            throw new ApiException("Aquesta tasca no pertany al grup", 400);
        }

        $task->assigned_id = $data['member_id'];
        $task->update();

        return response()->json(new TaskResource($task), 200);
    }

    /**
     * Comprova si l'usuari es admin
     *
     * @param  $group_id
     * @param  $task_id
     * @return void
     */
    public function complete($group_id, $task_id)
    {
        $member = $this->checkMember($group_id);
        $task = Task::findOrFail($task_id);

        if ($group_id != $task->group_id) {
            throw new ApiException("Aquesta tasca no pertany al grup", 400);
        }

        if ($task->assigned_id != $member->id) {
            return response()->json(['error' => 'No ets el propietari de la tasca'], 403);
        }

        $assigned = Member::findOrFail($task->assigned_id);

        DB::beginTransaction();
        try {

            $task->completed_date = new DateTime();
            $task->update();

            $assigned->balance += $task->value;
            $assigned->update();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return response()->json(new TaskResource($task), 200);
    }
}
