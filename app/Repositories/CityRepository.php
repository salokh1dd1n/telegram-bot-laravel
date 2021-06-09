<?php

namespace App\Repositories;

use App\Models\City as Model;

class CityRepository extends CoreRepository
{

    public function getModelClass()
    {
        return Model::class;
    }

    public function getCities($lang)
    {
        $columns = [
            'id',
            "title_$lang as title",
        ];
        $result = $this
            ->startConditions()
            ->select($columns)
            ->get();

        return $result;
    }

}