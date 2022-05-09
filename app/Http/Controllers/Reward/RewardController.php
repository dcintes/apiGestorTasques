<?php

namespace App\Http\Controllers\Reward;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\RewardCollection;
use App\Http\Resources\RewardResource;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * Get rewards of a group
     *
     * @param  $group_id
     * @return \Illuminate\Http\Response
     */
    public function list($group_id)
    {
        $this->checkMember($group_id);

        $rewards = Reward::where('group_id', $group_id)
            ->get();

        return response()->json(new RewardCollection($rewards), 200);
    }

    /**
     * Retorna una reward
     *
     * @param  $group_id
     * @param  $reward_id
     * @return \Illuminate\Http\Response
     */
    public function show($group_id, $reward_id)
    {
        $this->checkMember($group_id);

        $reward = Reward::findOrFail($reward_id);

        if ($group_id != $reward->group_id) {
            throw new ApiException("Aquesta recompensa no pertany al grup", 400);
        }

        return response()->json(new RewardResource($reward), 200);
    }
}
