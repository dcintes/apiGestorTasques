<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TemplateTaskCollection;
use App\Http\Resources\TemplateTaskResource;
use App\Models\Task;
use App\Models\Template_task;
use Illuminate\Http\Request;

class TemplateTaskController extends Controller
{
    /**
     * Get template tasks of a group
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function list($group_id)
    {
        $this->checkMember($group_id);

        $templateTasks = Template_task::where('group_id', $group_id)
            ->get();

        return response()->json(new TemplateTaskCollection($templateTasks), 200);
    }

    /**
     * Crea una template task
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $group_id)
    {
        $this->checkAdmin($group_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'value' => 'required|numeric'
        ]);

        $templateTask = new Template_task($data);
        $templateTask->group_id = $group_id;

        $templateTask->create();

        return response()->json(new TemplateTaskResource($templateTask), 201);
    }

    /**
     * Retorna una template task
     *
     * @param  $group_id
     * @param  $template_id
     * @return \Illuminate\Http\Response
     */
    public function show($group_id, $template_id)
    {
        $this->checkMember($group_id);

        $templateTask = Template_task::findOrFail($template_id);

        return response()->json(new TemplateTaskResource($templateTask), 200);
    }

    /**
     * Actualitza una tamplate task
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $group_id
     * @param  $template_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $group_id, $template_id)
    {
        $this->checkAdmin($group_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'value' => 'required|numeric'
        ]);

        $templateTask = Template_task::findOrFail($template_id);
        $templateTask->update($data);

        return response()->json(new TemplateTaskResource($templateTask), 200);
    }

    /**
     * Elimina una template task
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id, $template_id)
    {
        $this->checkAdmin($group_id);

        Template_task::findOrFail($template_id)->delete();

        return response()->json(null, 204);
    }

    /**
     * Crea una instancia de taska a partir de la plantilla
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function instance($group_id, $template_id)
    {
        $this->checkAdmin($group_id);

        $template = Template_task::findOrFail($template_id);

        $task = new Task($template->toArray());
        $task->group_id = $group_id;
        $task->template_id = $template->id;

        $task->create();

        return response()->json(new TaskResource($task), 201);
    }
}
