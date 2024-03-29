<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Service extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_services';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'code' => 'required|between:1,10',
        'name' => 'required|between:1,50|unique:kju_express_services',
        'weight_limit' => 'required|numeric|between:-1,999',
        'description' => '',
    ];

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->code->disabled = true;
        }
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->code}, {$this->name}";
    }
}
