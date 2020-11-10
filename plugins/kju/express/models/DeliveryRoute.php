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
    public $rules = [
        'src_district' => 'required',
        'dst_district' => 'required',
    
    ];

    public $belongsTo = [
        'src_district' => ['Kju\Express\Models\District'],
        'dst_district' => ['Kju\Express\Models\District']
    ];

    public $hasMany = [
        'delivery_costs' => ['Kju\Express\Models\DeliveryCost']
    ];


}
