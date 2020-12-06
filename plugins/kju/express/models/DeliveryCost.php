<?php

namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class DeliveryCost extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_costs';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'service' => 'required',
        'route' => 'required',
        'cost' => 'required|numeric|between:0,9999999',
        'add_cost' => 'numeric|between:0,999999',
        'min_lead_time' => 'numeric|between:1,99',
        'max_lead_time' => 'numeric|between:1,99'
    ];

    public $belongsTo = [
        'service' => ['Kju\Express\Models\Service', 'key' => 'service_code'],
        'route' => ['Kju\Express\Models\DeliveryRoute', 'key' => 'delivery_route_code']
    ];

    public function beforeValidate(){
        $this->rules['service_code'] = "required|unique:kju_express_delivery_costs,service_code,NULL,id,delivery_route_code,$this->delivery_route_code";
        $this->rules['max_lead_time'] = "numeric|between:$this->min_lead_time,99";
  
    }

    public function filterFields($fields, $context = null)
    {
        if (isset($this->service)) {
            if($this->service->weight_limit == -1){
                $fields->add_cost->hidden = true;
            }else{
                $fields->add_cost->hidden = false;
            }

        }else {
            $fields->add_cost->hidden = true;
        }
    }
}
