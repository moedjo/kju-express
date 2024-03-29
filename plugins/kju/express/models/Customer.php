<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Model;

/**
 * Model
 */
class Customer extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_customers';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => [
            'required',
            'regex:/^[\pL\s\-]+$/u',
        ],
        'phone_number' => [
            'required',
            'regex:/(\()?(\+62|62|0)(\d{2,3})?\)?[ .-]?\d{2,4}[ .-]?\d{2,4}[ .-]?\d{2,4}/',
        ],
        'email' => 'email'
    ];

    public $belongsTo = [
        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_id'],
        'user' => ['Kju\Express\Models\User', 'key' => 'user_id'],
    ];

    public function beforeValidate()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;
        if (isset($branch)) {
            $this->rules['phone_number'][] = 'unique:kju_express_customers,phone_number,NULL,id,branch_id,' . $branch->id;
        } else {
            $this->rules['phone_number'][] = 'unique:kju_express_customers,phone_number,NULL,id,branch_id,NULL';
    
        }
    }


    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;

        $this->user = $user;
        if (isset($branch)) {
            $this->branch = $branch;
        }
    }

    public function scopeDeliveryOrderCustomer($query, $model)
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;
        if ($user->isSuperUser()) {
            return $query;
        } else if (isset($branch)) {
            return $query->where('branch_id', $branch->id);
        } else {
            return $query->where('user_id', $user->id);;
        }
    }
}
