<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Package extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_package';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
