<?php

namespace Kju\Express\Models;

use Model;
use October\Rain\Exception\ApplicationException;

/**
 * Model
 */
class Balance extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Purgeable;
    // use \October\Rain\Database\Traits\Revisionable;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_balances';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'balance' => 'numeric'
    ];

    protected $purgeable = ['type'];

    public $hasMany = [
        'transactions' => ['Kju\Express\Models\Transaction']
    ];

    public $morphTo = [
        'owner' => []
    ];

    public function beforeValidate()
    {
    }
}
