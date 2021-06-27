<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Model;

/**
 * Model
 */
class IntDeliveryCost extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Revisionable;

    protected $revisionable = [
        'min_range_weight', 'max_range_weight', 'base_cost_per_kg', 'profit_percentage', 'int_delivery_route_id', 'updated_at',
       'created_at','updated_at'
    ];
    
    public $morphMany = [
        'revision_history' => ['System\Models\Revision', 'name' => 'revisionable']
    ];

    public function getRevisionableUser()
    {
        return BackendAuth::getUser();
    }


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_int_delivery_costs';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'min_range_weight' => 'required',
        'max_range_weight' => 'required',
        'base_cost_per_kg' => 'required|numeric|min:1000',
        'profit_percentage' => 'required|numeric|between:1,100',
    ];


    public $belongsTo = [
        'route' => ['Kju\Express\Models\IntDeliveryRoute', 'key' => 'int_delivery_route_id']
    ];

    public function beforeValidate()
    {
        $this->rules['max_range_weight'] = "required|numeric|between:$this->min_range_weight,10000";

        $int_delivery_cost = IntDeliveryCost::where('int_delivery_route_id', $this->route->id)
            ->whereRaw("($this->min_range_weight BETWEEN min_range_weight AND max_range_weight OR $this->max_range_weight BETWEEN min_range_weight AND max_range_weight)")
            ->first();


        if (isset($int_delivery_cost)) {

            if (($int_delivery_cost->min_range_weight == $this->min_range_weight)
                && ($int_delivery_cost->max_range_weight  == $this->max_range_weight)
            ) {
            } else {

                $this->rules['min_range_weight'] = 'required|accepted';
            }
        }
    }
}
