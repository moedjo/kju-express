<?php

namespace Kju\Express\Models;

use Illuminate\Support\Facades\Log;
use Model;

/**
 * Model
 */
class Region extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_regions';
    public $incrementing = false;

    /**
     * @var array Validation rules
     */
    public $rules = [

        'parent' => 'required_if:type,district,type,regency'
    ];

    public $belongsTo = [
        'parent' => ['Kju\Express\Models\Region', 'key' => 'parent_id']
    ];

    public function getTypeOptions()
    {
        return [
            'province' => 'kju.express::lang.global.province',
            'regency' => 'kju.express::lang.global.regency',
            'district' => 'kju.express::lang.global.district',
        ];
    }


    public function scopeType($query, $model)
    {
        $type = $model->type;
        if (empty($type)) {
            $type = input()['Region']['type'];
        }

        if ($type == 'regency') {
            return $query->where('type', 'province');
        } else  if ($type == 'district') {
            return $query->where('type', 'regency');
        } else {
            return $query->where('type', '');
        }
    }

    public function getDisplayTypeAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->type));
    }


    public function getDisplayNameAttribute()
    {
        if ($this->type ==  'district') {
            return "{$this->name}, {$this->parent->name}";
        }
        return $this->name;
    }


    public function filterFields($fields, $context = null)
    {
        if ($context == 'update') {
            $fields->id->readOnly = true;
            $fields->type->disabled = true;
        }
    }

    public function scopeLevelDistrict($query, $model)
    {
        return $query->where(function($query){
            $query->where('type', "regency")
                ->orWhere('type', "district");
        });
    }
}
