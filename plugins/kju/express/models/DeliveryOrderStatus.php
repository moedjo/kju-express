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
        'status' => 'required'
    ];


    public $belongsTo = [
         'order' => ['Kju\Express\Models\DeliveryOrder', 'key' => 'delivery_order_code'],
         'region' => ['Kju\Express\Models\Region'],
         'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
      
    ];



    public function afterCreate(){
    
        // $order = $this->order;


        // $order->consignee_phone_number = 'blee';
        // $order->save();

    }


    public function getStatusOptions()
    {
        return [
            // 'pickup' => 'kju.express::lang.global.pickup',
            // 'process' => 'kju.express::lang.global.process',
            'transit' => 'kju.express::lang.global.transit',
            'received' => 'kju.express::lang.global.received',
            'failed' => 'kju.express::lang.global.failed',
        ];
    }
}
