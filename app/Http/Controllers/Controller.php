<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Models\Member;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Valida les dades d'entrada
     * @param array $data dades a validar
     * @param array $rules validacions
     * @return array dades validades
     */
    protected function validate(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ApiException($validator->errors(), 400);
        }

        return $validator->validated();
    }

    /**
     * Valida que l'usuari logedjat es correspon al del id
     * @param int $user_id id usuario
     */
    protected function isLogedUser($user_id)
    {
        if (!auth()->user()->id == $user_id) {
            throw throw new ApiException("Forbidden", 403);
        }

        return true;
    }

    /**
     * Valida que l'usuari logedjat es membre del grup
     * @param int $group_id id del grup
     * @return Member
     */
    protected function checkMember($group_id)
    {
        $member = Member::where('group', $group_id)
            ->where('user', auth()->user()->id)
            ->first();

        if (!(array)$member) {
            throw throw new ApiException("Forbidden", 403);
        }

        return $member;
    }

    /**
     * Valida que l'usuari logedjat es admin del grup
     * @param int $group_id id del grup
     * @return Member
     */
    protected function checkAdmin($group_id)
    {
        $member = $this->checkMember($group_id);

        if (!$member->admin) {
            throw throw new ApiException("Forbidden", 403);
        }

        return $member;
    }
}
