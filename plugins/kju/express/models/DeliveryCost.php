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
        'delivery_route' => 'required',
        'cost' => 'required|numeric|between:0,9999999',
        'add_cost' => 'numeric|between:0,999999',
        'min_lead_time' => 'numeric|between:1,99',
        'max_lead_time' => 'numeric|between:1,99'
    ];

    // public $morphTo = [
    //     'delivery_route' => []
    // ];

    public $belongsTo = [
        'service' => ['Kju\Express\Models\Service', 'key' => 'service_code'],
        'delivery_route' => ['Kju\Express\Models\DeliveryRoute']
    ];


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
