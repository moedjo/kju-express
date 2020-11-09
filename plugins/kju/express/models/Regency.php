<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Regency extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_regencies';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        'id' => 'required|numeric',
        'name' => 'required|between:1,100|unique:kju_express_regencies',
        'province' => 'required',
    ];

    public $belongsTo = [
        'province' => ['Kju\Express\Models\Province']
    ];

    public function getDisplayNameAttribute()
    {
        return "{$this->name}, {$this->province->name}";
    }

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->id->readOnly = true;
        }
    }
}
