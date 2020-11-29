<?php namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class DeliveryOrderStatus extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_order_statuses';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];


    public $belongsTo = [
         'order' => ['Kju\Express\Models\DeliveryOrder', 'key' => 'delivery_order_code']
    ];

    public function afterCreate(){
    
        $order = $this->order;


        $order->consignee_phone_number = 'blee';
        $order->save();

    }
}
