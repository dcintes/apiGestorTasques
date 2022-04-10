<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use DateTime;
use Illuminate\Http\Request;

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

        $task->create();

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

        Task::findOrFail($task_id)->delete();

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

        if ($task->assigned_id != $member->id) {
            return response()->json(['error' => 'No ets el propietari de la tasca'], 403);
        }

        $task->completed_date = new DateTime();
        $task->update();

        return response()->json(new TaskResource($task), 200);
    }
}
