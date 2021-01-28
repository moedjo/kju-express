<?php namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Model;

/**
 * Model
 */
class IntDeliveryOrderStatus extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_int_delivery_order_statuses';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'status' => 'required'
    ];


    public $belongsTo = [
         'order' => ['Kju\Express\Models\IntDeliveryOrder', 'key' => 'int_delivery_order_code'],
         'region' => ['Kju\Express\Models\Region'],
         'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
      
    ];

    protected $fillable = [
        'int_delivery_order_code',
        'description' ,
        'status' ,
    ];

    public function afterCreate(){
        $order = $this->order;
        $order->status = $this->status;
        $order->save();
    }

    public function beforeSave(){
        $user = BackendAuth::getUser();
        $this->created_user = $user;
    }


    public function getStatusOptions()
    {
        return [
            'process' => 'kju.express::lang.global.process',
            'transit' => 'kju.express::lang.global.transit',
            'received' => 'kju.express::lang.global.received',
            'failed' => 'kju.express::lang.global.failed',
        ];
    }
}