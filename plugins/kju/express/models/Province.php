<?php

namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Province extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_provinces';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        'id' => 'required|numeric|between:11,99',
        'name' => 'required|between:1,100|unique:kju_express_provinces',
    ];


    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->id->readOnly = true;
        }
    }

    public function getProvinceOptions()
    {
        return Province::lists('name', 'id');
    }
}
