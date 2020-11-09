<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Service extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_services';

    protected $primaryKey = 'code';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        'code' => 'required|between:1,10',
        'name' => 'required|between:1,50|unique:kju_express_provinces',
        'description' => 'required|between:1,100',
    ];

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->code->readOnly = true;
        }
    }
}
