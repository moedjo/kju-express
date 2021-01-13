<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
            'country' => 'kju.express::lang.global.country',
            'province' => 'kju.express::lang.global.province',
            'regency' => 'kju.express::lang.global.regency',
            'district' => 'kju.express::lang.global.district',
        ];
    }

    public function scopeParent($query)
    {
        $type = post('Region[type]');

        if (isset($type)) {
            Session::put('Region[type]', $type);
        } else {
            $type = Session::get('Region[type]');
        }

        if ($type == 'regency') {
            return $query->where('type', 'province');
        }

        if ($type == 'district') {
            return $query->where('type', 'regency');
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
            $fields->id->disabled = true;
            $fields->type->disabled = true;
        }
    }

    public function scopeDeliveryOrderDistrict($query, $model)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;
        if ($user->isSuperUser()) {
            return $query;
        } else  if (isset($branch)) {
            return $query->where('parent_id', $branch->region->id);
        } else {
            return $query->where('parent_id', '-1');;
        }
    }
}
