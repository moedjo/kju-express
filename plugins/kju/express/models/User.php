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

    public $belongsTo = [
        'role' => UserRole::class,
        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_code'],
    ];

    public function beforeValidate()
    {
        $this->rules['role'] = 'required';
        $this->rules['branch'] = 'required';
    }

    public function scopeCourier($query, $model)
    {
        return $query->whereHas('role', function ($query) {
            $query->where('code', 'courier');
        });
    }

    public function getRoleOptions()
    {
        $user = BackendAuth::getUser();
        $result = [];
        $roles = null;
        if ($user->isSuperUser()) {
            $roles = UserRole::all();
        } else if ($user->role->code == 'supervisor') {
            $roles = UserRole::whereIn('code', ['operator', 'courier'])->get();
        }
        foreach ($roles as $role) {
            $result[$role->id] = [$role->name, $role->description];
        }
        return $result;
    }

    public function beforeCreate(){
        $user = BackendAuth::getUser();
        if (!$user->isSuperUser()) {
            if ($user->role->code == 'supervisor') {
                $this->branch = $user->branch;
            }
        }
    }

    public function filterFields($fields, $context = null)
    {
        $user = BackendAuth::getUser();
        if (!$user->isSuperUser()) {
            $fields->branch->disabled = true;
        }
    }
}
