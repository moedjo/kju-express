<?php

namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class DeliveryRoute extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_routes';

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $belongsTo = [
        'branch' => ['Kju\Express\Models\Branch'],
        'district' => ['Kju\Express\Models\District']
    ];

    public $morphMany = [
        'delivery_costs' => ['Kju\Express\Models\DeliveryCost', 'name' => 'delivery_route']
    ];
}
