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

    // public $hasOne = [
    //     'branch' =>['Kju\Express\Models\Branch'],
    //     'user' => 'Kju\Express\Models\User',
    // ];

    protected $purgeable = ['type'];

    public $hasMany = [
        'transactions' => ['Kju\Express\Models\Transaction']
    ];

    public $morphTo = [
        'owner' => []
    ];

    public function beforeValidate()
    {
        if ($this->type == 'branch') {
            // TODO FIX update id bro
            $this->rules['branch'] = ['required'];

            // trace_log($this->branch);
            if (empty($this->branch)) {
                // throw new ApplicationException(e(trans('kju.express::lang.global.action_not_allowed')));
                return;
            }

            $branch_balance = Balance::whereHas('branch', function ($query) {
                $query->where('id', $this->branch->id);
            })->get()->toArray();

            if (!empty($branch_balance)) {
                throw new ApplicationException(e(trans('kju.express::lang.global.action_not_allowed')));
            }
        }

        if ($this->type == 'user') {
            $this->rules['user'] = 'required';

            if (empty($this->user)) {
                return;
            }

            $user_balance = Balance::whereHas('user', function ($query) {
                $query->where('id', $this->user->id);
            })->get()->toArray();

            if (!empty($user_balance)) {
                throw new ApplicationException(e(trans('kju.express::lang.global.action_not_allowed')));
            }
        }
    }
}
