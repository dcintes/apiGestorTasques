<?php

namespace App\Http\Controllers\Reward;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\RewardResource;
use App\Http\Resources\TemplateRewardCollection;
use App\Http\Resources\TemplateRewardResource;
use App\Models\Member;
use App\Models\Reward;
use App\Models\Template_reward;
use App\Models\Template_task;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateRewardController extends Controller
{
    /**
     * Get template rewards of a group
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function list($group_id)
    {
        $this->checkMember($group_id);

        $templateRewards = Template_reward::where('group_id', $group_id)
            ->get();

        return response()->json(new TemplateRewardCollection($templateRewards), 200);
    }

    /**
     * Crea una template reward
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $group_id)
    {
        $this->checkAdmin($group_id);

        $data = $this->validate($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'cost' => 'required|numeric',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:10'
        ]);

        $templateReward = new Template_reward($data);
        $templateReward->group_id = $group_id;

        $templateReward->save();

        return response()->json(new TemplateRewardResource($templateReward), 201);
    }

    /**
     * Retorna una template reward
     *
     * @param  $group_id
     * @param  $template_id
     * @return \Illuminate\Http\Response
     */
    public function show($group_id, $template_id)
    {
        $this->checkMember($group_id);

        $templateReward = Template_reward::findOrFail($template_id);

        if ($group_id != $templateReward->group_id) {
            throw new ApiException("Aquesta recompensa no pertany al grup", 400);
        }

        return response()->json(new TemplateRewardResource($templateReward), 200);
    }

    /**
     * Actualitza una tamplate reward
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
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'cost' => 'required|numeric',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:10'
        ]);

        $templateReward = Template_reward::findOrFail($template_id);

        if ($group_id != $templateReward->group_id) {
            throw new ApiException("Aquesta recompensa no pertany al grup", 400);
        }

        $templateReward->update($data);

        return response()->json(new TemplateRewardResource($templateReward), 200);
    }

    /**
     * Elimina una template reward
     *
     * @param  $group_id
     * @param  $template_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id, $template_id)
    {
        $this->checkAdmin($group_id);

        $templateReward = Template_reward::findOrFail($template_id);

        if ($group_id != $templateReward->group_id) {
            throw new ApiException("Aquesta recompensa no pertany al grup", 400);
        }

        DB::beginTransaction();
        try {

            Reward::where('template_id', '=', $templateReward->id)
                ->update(['template_id' => null]);

            $templateReward->delete();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }



        return response()->json(null, 204);
    }

    /**
     * Crea una instancia de reward a partir de la plantilla
     *
     * @param  $group_id
     * @param  $template_id
     * @return \Illuminate\Http\Response
     */
    public function claim($group_id, $template_id)
    {
        $member = $this->checkMember($group_id);

        $template = Template_reward::findOrFail($template_id);

        if ($group_id != $template->group_id) {
            throw new ApiException("Aquesta recompensa no pertany al grup", 400);
        }

        // Validam que l'usuari te saldo suficient
        if ($member->balance < $template->cost) {
            return response()->json(['error' => 'No té saldo suficient'], 400);
        }

        // Cream recompensa
        $reward = new Reward($template->toArray());
        $reward->group_id = $group_id;
        $reward->template_id = $template_id;
        $reward->claimer_id = $member->id;
        $reward->claimed_date = new DateTime();

        DB::beginTransaction();
        try {
            $member->balance -= $template->cost;
            $member->update();

            $reward->save();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return response()->json(new RewardResource($reward), 201);
    }
}
