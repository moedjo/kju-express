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
    ];
}
