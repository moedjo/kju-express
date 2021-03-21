<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Model;

/**
 * Model
 */
class IntAddDeliveryCost extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Revisionable;

    protected $revisionable = [
        'add_cost_per_kg', 'goods_type_code', 
        'int_delivery_route_code', 'created_at', 'updated_at'
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
    public $table = 'kju_express_int_add_delivery_costs';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'goods_type' => 'required',
        'add_cost_per_kg' => 'required|numeric|min:1000'
    ];

    public $belongsTo = [
        'route' => ['Kju\Express\Models\IntDeliveryRoute', 'key' => 'int_delivery_route_code'],
        'goods_type' => ['Kju\Express\Models\GoodsType', 'key' => 'goods_type_code'],
    ];
}
