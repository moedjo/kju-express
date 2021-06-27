<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Backend\Models\UserRole;
use Model;

/**
 * Model
 */
class User extends \Backend\Models\User
{


    public $rules = [
        'email' => 'required|between:6,255|email|unique:backend_users',
        'login' => [
            'required', 'between:2,255', 'unique:backend_users',
            'regex:/^[a-zA-Z0-9]([._-](?![._-])|[a-zA-Z0-9]){3,18}[a-zA-Z0-9]$/'
        ],
        'password' => 'required:create|between:4,255|confirmed',
        'password_confirmation' => 'required_with:password|between:4,255'
    ];
    public $belongsTo = [
        'role' => UserRole::class,
        'branch' => ['Kju\Express\Models\Branch'],
    ];

    public $morphMany = [
        'transactions' => ['Kju\Express\Models\Transaction', 'name' => 'transactionable']
    ];

    public $morphOne = [
        'balance' => ['Kju\Express\Models\Balance', 'name' => 'owner']
    ];

    public function beforeValidate()
    {
        $this->rules['role'] = 'required';
    }

    public function scopeDeliveryOrderCourier($query, $model)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;
        if ($user->isSuperUser()) {
            return $query->whereHas('role', function ($query) {
                $query->where('code', 'courier');
            });
        } else if (isset($branch)) {
            return $query->where('branch_id', $branch->id)->whereHas('role', function ($query) {
                $query->where('code', 'courier');
            });
        } else {
            return $query->where('branch_id', null)->whereHas('role', function ($query) {
                $query->where('code', 'courier');
            });
        }
    }

    public function getRoleOptions()
    {
        $user = BackendAuth::getUser();
        $result = [];
        $roles = null;
        if ($user->isSuperUser()) {
            $roles = UserRole::whereIn('code', [
                'supervisor',
                'operator',
                'courier',
                'checker',
                'tracker',
            ])->get();
        } else if ($user->hasPermission([
            'is_supervisor'
        ])) {
            $roles = UserRole::whereIn('code', ['operator', 'courier'])->get();
        }
        foreach ($roles as $role) {
            $result[$role->id] = [$role->name, $role->description];
        }
        return $result;
    }

    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        if ($user->isSuperUser()) {
        } else if ($user->hasPermission(['is_supervisor'])) {
            $this->branch = $user->branch;
        }
    }

    public function afterCreate(){
        $balance = new Balance();
        $balance->balance = 0;
        $balance->owner()->associate($this);
        $balance->save();
    }
}
