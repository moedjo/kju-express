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


    public function beforeValidate(){
        $this->rules['role'] = 'required';
    }

    public function getRoleOptions()
    {
        $user = BackendAuth::getUser();
        $roles = [];

        if ($user->hasAccess('access_users_add_role_courier')) {
            $role = UserRole::where('code', 'courier')->first();
            if (isset($role)) {
                $roles[$role->id] = [$role->name, $role->description];
            }
        }

        if ($user->hasAccess('access_users_add_role_operator')) {
            $role = UserRole::where('code', 'operator')->first();
            if (isset($role)) {
                $roles[$role->id] = [$role->name, $role->description];
            }
        }

        if ($user->hasAccess('access_users_add_role_supervisor')) {
            $role = UserRole::where('code', 'supervisor')->first();
            if (isset($role)) {
                $roles[$role->id] = [$role->name, $role->description];
            }
        }
        return $roles;
    }
}
