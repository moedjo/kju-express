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
    protected $primaryKey = 'code';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
        'src_region' => 'required',
        'dst_region' => 'required',
    
    ];

    public $belongsTo = [
        'src_region' => ['Kju\Express\Models\Region','key' => 'src_region_id'],
        'dst_region' => ['Kju\Express\Models\Region','key' => 'dst_region_id']
    ];

    public $hasMany = [
        'delivery_costs' => ['Kju\Express\Models\DeliveryCost']
    ];


    public function beforeCreate(){
        
        $this->code = $this->src_region->id.'-'.$this->dst_region->id;
    }

    public function beforeValidate(){
        $this->rules['src_region'] = "required|unique:kju_express_delivery_routes,src_region_id,NULL,code,dst_region_id,$this->dst_region_id";
    }

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->src_region->disabled = true;
            $fields->dst_region->disabled = true;
        }
    }

}
