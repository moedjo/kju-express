<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class DeliveryOrder extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_orders';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsTo = [
        'branch_region' => ['Kju\Express\Models\Region','key' => 'branch_region_id'],
        'pickup_region' => ['Kju\Express\Models\Region','key' => 'pickup_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region','key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer','key' => 'customer_id'],
    ];
}
