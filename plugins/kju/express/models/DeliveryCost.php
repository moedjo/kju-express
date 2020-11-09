<?php namespace Kju\Express\Models;

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
    ];

    public $morphTo = [
        'delivery_route' => []
    ];

    public $belongsTo = [
        'service' => ['Kju\Express\Models\Service','key' => 'service_code'],
    ];

}
