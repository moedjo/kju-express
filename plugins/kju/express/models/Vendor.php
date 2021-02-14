<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Vendor extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_vendors';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required|between:1,100|unique:kju_express_vendors',
    ];
}
