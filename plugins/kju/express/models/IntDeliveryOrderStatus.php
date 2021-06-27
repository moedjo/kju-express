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
         'order' => ['Kju\Express\Models\IntDeliveryOrder', 'key' => 'int_delivery_order_id'],
         'region' => ['Kju\Express\Models\Region'],
         'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
      
    ];


    public function afterCreate(){
    }

    public function beforeCreate(){
        $user = BackendAuth::getUser();
        $this->created_user = $user;
    }


    public function getStatusOptions()
    {
        return [
            'pending' => 'kju.express::lang.global.pending',
            'process' => 'kju.express::lang.global.process',
            'export' => 'kju.express::lang.global.export',
            'reject' => 'kju.express::lang.global.reject',
            'failed' => 'kju.express::lang.global.failed',
        ];
    }
}
