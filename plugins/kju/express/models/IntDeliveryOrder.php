<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Carbon\Carbon;
use Exception;
use Kju\Express\classes\IdGenerator;
use Model;
use October\Rain\Exception\ApplicationException;
use October\Rain\Support\Facades\Flash;

/**
 * Model
 */
class IntDeliveryOrder extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Purgeable;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_int_delivery_orders';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $purgeable = ['total_cost_agreement', 'discount_agreement'];

    protected $dates = ['deleted_at', 'process_at', 'received_at'];
    /**
     * @var array Validation rules
     */
    public $rules = [];

    const VOLUME_DIVIDER = 5000;

    public $belongsTo = [
        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_code'],
        'origin_region' => ['Kju\Express\Models\Region', 'key' => 'origin_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region', 'key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer', 'key' => 'customer_id'],

        'goods_type' => ['Kju\Express\Models\GoodsType', 'key' => 'goods_type_code'],

        'updated_user' => ['Kju\Express\Models\User', 'key' => 'updated_user_id'],
        'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
        'deleted_user' => ['Kju\Express\Models\User', 'key' => 'deleted_user_id'],

    ];


    private function initWeight()
    {

        $height = $this->goods_height;
        $width = $this->goods_width;
        $length = $this->goods_length;

        $weight = ($height *
            $width *
            $length) / self::VOLUME_DIVIDER;

        $this->goods_weight = $weight;
    }


    private function initTotalCost()
    {
        $user = BackendAuth::getUser();

        $weight = ceil($this->goods_weight);

        $route_code = $this->origin_region->id . '-' . $this->consignee_region->id;
        $goods_type_code = $this->goods_type->code;

        $int_delivery_cost = IntDeliveryCost::where('int_delivery_route_code', $route_code)
            ->whereRaw("$weight BETWEEN min_range_weight AND max_range_weight")
            ->first();

        if (isset($int_delivery_cost)) {
            $total_cost = $int_delivery_cost->base_cost_per_kg * $weight;
            $total_cost = $total_cost +
                ($total_cost * ($int_delivery_cost->profit_percentage / 100));


            $int_add_delivery_cost = IntAddDeliveryCost::where('int_delivery_route_code', $route_code)
                ->where('goods_type_code', $goods_type_code)
                ->first();

            if (isset($int_add_delivery_cost)) {
                $add_cost = $int_add_delivery_cost->add_cost_per_kg * $weight;
                $total_cost = $total_cost + $add_cost;

                $this->add_cost_per_kg = $int_delivery_cost->add_cost_per_kg;
            }

            $this->total_cost = $total_cost;

            $this->original_total_cost = $this->total_cost + 0;
            
            $total_discount =  $this->total_cost * ($this->discount / 100);
            $this->total_cost = $this->total_cost - $total_discount;

            $this->int_delivery_route_code = $int_delivery_cost->int_delivery_route_code;
            $this->min_range_weight = $int_delivery_cost->min_range_weight;
            $this->max_range_weight = $int_delivery_cost->max_range_weight;
            $this->base_cost_per_kg = $int_delivery_cost->base_cost_per_kg;
            $this->profit_percentage = $int_delivery_cost->profit_percentage;
        } else {
            $this->total_cost = null;
        }
    }


    public function filterFields($fields, $context = null)
    {
        if (
            $this->goods_height &&
            $this->goods_width &&
            $this->goods_length &&

            is_numeric($this->goods_height) &&
            is_numeric($this->goods_width) &&
            is_numeric($this->goods_length)
        ) {
            $this->initWeight();
            $fields->goods_weight->value = $this->goods_weight;
        } else {
            $this->goods_weight = 0;
            $fields->goods_weight->value = 0;
        }

        if (
            isset($this->origin_region) &&
            isset($this->consignee_region) &&
            isset($this->goods_type) &&
            $this->goods_weight &&

            is_numeric($this->discount)
        ) {

            $this->initTotalCost();

            if ($this->total_cost) {
                $fields->total_cost->value = $this->total_cost;
            } else {
                Flash::warning(e(trans('kju.express::lang.global.service_not_available')));
                $fields->total_cost->value = 0;
            }
        } else {
            $this->total_cost = 0;
            $fields->total_cost->value = 0;
        }
    }

    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;

        //INITIALIZE CODE
        $code = IdGenerator::alpha(isset($this->branch) ? $this->branch->code : '', 4)
            . $this->origin_region->id
            . IdGenerator::numeric(isset($this->branch) ? $this->branch->code : '', 4);
        $this->code = $code;

        $this->created_user = $user;

        //SET BRANCH & BRANCH REGION
        if (isset($branch)) {
            $this->branch = $branch;
            $this->origin_region = $branch->region->parent;
        }

        // SET INIT STATUS
        $this->status = 'process';
        $this->process_at = Carbon::now();
    }

    public function beforeSave()
    {
        $this->initWeight();
        $this->initTotalCost();

        if (!$this->total_cost) {
            throw new ApplicationException(e(trans('kju.express::lang.global.service_not_available')));
        }
    }

    public function beforeUpdate()
    {
        $user = BackendAuth::getUser();
        $this->updated_user = $user;
    }

    public function beforeDelete()
    {
        $user = BackendAuth::getUser();
        if ($user->isSuperUser()) {
            // Nothing
        } else if ($user->hasPermission('is_supervisor')) {
            if ($this->status == 'process') {
                if ($this->created_at->diffInMinutes(new Carbon()) >= 120) {
                    throw new ApplicationException(e(trans('kju.express::lang.global.delete_not_allowed')));
                }
            } else {
                throw new ApplicationException(e(trans('kju.express::lang.global.delete_not_allowed')));
            }
        } else {
            throw new ApplicationException(e(trans('kju.express::lang.global.delete_not_allowed')));
        }
    }

    public function afterDelete()
    {
        $user = BackendAuth::getUser();
        $this->deleted_user = $user;
        $this->save();
    }

    public function afterUpdate()
    {
        // $user = BackendAuth::getUser();
        // if ($user->hasPermission('is_courier')) {
        //     if ($this->original['status'] == 'pickup' && $this->status == 'process') {
        //         $order_status = new DeliveryOrderStatus();
        //         $order_status->region = $this->branch_region;
        //         $order_status->status = $this->status;
        //         $order_status->created_user = $this->created_user;
        //         $order_status->delivery_order_code = $this->code;

        //         $order_status->save();
        //     }
        // }
    }


    public function afterCreate()
    {
        // $order_status = new DeliveryOrderStatus();
        // $order_status->region = $this->branch_region;
        // $order_status->status = $this->status;
        // $order_status->created_user = $this->created_user;
        // $order_status->delivery_order_code = $this->code;
        // $order_status->save();
    }

    public function getDisplayStatusAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->status));
    }

    public function getDisplayPaymentStatusAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->payment_status));
    }
}
