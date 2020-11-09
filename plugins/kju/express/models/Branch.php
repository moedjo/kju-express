<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Branch extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_branches';
    protected $primaryKey = 'code';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        'code' => 'required|between:1,10',
        'name' => 'required|between:1,100|unique:kju_express_provinces',
        'district' => 'required',
    ];

    public $belongsTo = [
        'district' => ['Kju\Express\Models\District']
    ];

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->code->readOnly = true;
        }
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->code}, {$this->name}";
    }

}
