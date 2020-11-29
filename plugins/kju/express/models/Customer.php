<?php namespace Kju\Express\Models;

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
            'regex:/(?:\+62)?0?8\d{2}(\d{8})/',
        ],
        'email' => 'email'
    ];
}
