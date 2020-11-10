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
        'src_regency' => 'required',
        'dst_district' => 'required',
    
    ];

    public $belongsTo = [
        'src_regency' => ['Kju\Express\Models\Regency'],
        'dst_district' => ['Kju\Express\Models\District']
    ];

    public $hasMany = [
        'delivery_costs' => ['Kju\Express\Models\DeliveryCost']
    ];


    public function beforeCreate(){
        
        $this->code = $this->src_regency->id.'-'.$this->dst_district->id;
    }

    public function beforeValidate(){
        $this->rules['src_regency_id'] = "required|unique:kju_express_delivery_routes,src_regency_id,NULL,code,dst_district_id,$this->dst_district_id";
    }

    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->src_regency->readOnly = true;
            $fields->dst_district->readOnly = true;
        }
    }

}
