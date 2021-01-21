<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class IntDeliveryOrder extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_int_delivery_orders';
    protected $primaryKey = 'code';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        
    ]; 

    public $belongsTo = [
        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_code'],
        'branch_region' => ['Kju\Express\Models\Region', 'key' => 'branch_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region', 'key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer', 'key' => 'customer_id'],

    ];
}
