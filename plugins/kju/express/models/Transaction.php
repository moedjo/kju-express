<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Model;

/**
 * Model
 */
class Transaction extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_transactions';

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $belongsTo = [
        'balance' => ['Kju\Express\Models\Balance', 'key' => 'balance_id'],
        'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
    ];

    public $morphTo = [
        'transactionable' => []
    ];

    public function beforeCreate(){
        $this->last_balance = $this->balance->balance;
        $this->current_balance = $this->last_balance + $this->amount;

        $this->transactionable()->associate($this->balance->owner);
        $this->created_user = BackendAuth::getUser();

    }

    public function afterCreate()
    {
        $this->balance->balance = $this->current_balance;
        $this->balance->save();

        

    }
}
