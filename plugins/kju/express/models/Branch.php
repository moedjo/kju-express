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

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
