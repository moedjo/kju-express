<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class District extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_districts';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        'id' => 'required|numeric|between:110101,999999',
        'name' => 'required|between:1,100|unique:kju_express_districts',
        'regency' => 'required',
    ];

    public $belongsTo = [
        'regency' => ['Kju\Express\Models\Regency']
    ];

    public function getDisplayNameAttribute()
    {
        return "{$this->name}, {$this->regency->name}, {$this->regency->province->name}";
    }

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->id->readOnly = true;
        }
    }
}
