<?php

namespace App\Repositories;

use App\Models\User as Model;

class UserRepository extends CoreRepository
{

    public function getModelClass()
    {
        return Model::class;
    }

    public function getUser($telegram_id)
    {
        $result = $this
            ->startConditions()
            ->where('telegram_id', $telegram_id)
            ->first();

        return $result;
    }

    public function addUser($data)
    {
        $result = $this
            ->startConditions()
            ->create($data);

        return $result;
    }

    public function editLang($id, $data)
    {
        $result = $this
            ->startConditions()
            ->findOrFail($id)
            ->update(['language_code' => $data]);

        return $result;
    }

    public function addPhoneNumber($data)
    {
        $result = $this
            ->startConditions()
            ->create(['phone_number' => $data]);

        return $result;
    }

}