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

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
