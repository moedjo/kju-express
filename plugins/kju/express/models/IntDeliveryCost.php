<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class IntDeliveryCost extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_int_delivery_costs';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'min_range_weight'=>'required',
        'max_range_weight'=>'required',
        'base_cost_per_kg'=>'required|numeric|min:1000',
        'profit_percentage'=>'required|numeric|between:1,100',
    ];


    public $belongsTo = [
         'route' => ['Kju\Express\Models\IntDeliveryRoute', 'key' => 'int_delivery_route_code']
    ];

    public function beforeValidate(){
         $this->rules['max_range_weight'] = "required|numeric|between:$this->min_range_weight,10000";
    }
}
