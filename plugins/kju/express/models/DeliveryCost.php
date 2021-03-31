<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Model;

/**
 * Model
 */
class DeliveryCost extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Revisionable;

    protected $revisionable = [
        'cost', 'add_cost', 'min_lead_time', 'max_lead_time', 'created_at', 'updated_at',
        'delivery_route_id', 'service_id'
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
        'service' => ['Kju\Express\Models\Service'],
        'route' => ['Kju\Express\Models\DeliveryRoute','key' => 'delivery_route_id']
    ];

    public function beforeValidate()
    {
        $this->rules['service_id'] = "required|unique:kju_express_delivery_costs,service_id,NULL,id,delivery_route_id,$this->delivery_route_id";
        $this->rules['max_lead_time'] = "numeric|between:$this->min_lead_time,99";
    }

    public function filterFields($fields, $context = null)
    {
        if (isset($this->service)) {
            if ($this->service->weight_limit == -1) {
                $fields->add_cost->hidden = true;
            } else {
                $fields->add_cost->hidden = false;
            }
        } else {
            $fields->add_cost->hidden = true;
        }
    }
}
