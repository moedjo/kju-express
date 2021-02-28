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
    use \October\Rain\Database\Traits\Revisionable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_int_delivery_orders';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $purgeable = ['total_cost_agreement', 'checker_action'];

    protected $dates = ['deleted_at', 'process_at', 'received_at'];

    protected $revisionable = [
        'goods_description', 'goods_amount', 'goods_weight', 'goods_volume_weight',
        'goods_height', 'goods_width', 'goods_length',
        'original_total_cost', 'base_cost', 'add_cost', 'total_cost',
        'base_profit', 'profit', 'net_profit', 'goods_type_profit_share', 'fee', 'fee_percentage',
        'different_total_cost'
    ];

    public $morphMany = [
        'revision_history' => ['System\Models\Revision', 'name' => 'revisionable']
    ];
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

        'vendor' => ['Kju\Express\Models\Vendor', 'key' => 'vendor_id'],

    ];

    public $hasMany = [
        'statuses' => ['Kju\Express\Models\IntDeliveryOrderStatus']
    ];


    private function initWeight()
    {

        $height = $this->goods_height;
        $width = $this->goods_width;
        $length = $this->goods_length;

        $weight = ($height *
            $width *
            $length) / self::VOLUME_DIVIDER;

        $this->goods_volume_weight = $weight;
    }


    private function initTotalCost()
    {
        $user = BackendAuth::getUser();
        $fee_percentage = 0;
        $branch = $user->branch;
        $weight = 0;

        if ($this->goods_weight > $this->goods_volume_weight) {
            $weight = ceil($this->goods_weight);
        } else {
            $weight = ceil($this->goods_volume_weight);
        }

        $route_code = $this->origin_region->id . '-' . $this->consignee_region->id;
        $goods_type_code = $this->goods_type->code;


        $int_delivery_cost = IntDeliveryCost::where('int_delivery_route_code', $route_code)
            ->whereRaw("$weight BETWEEN min_range_weight AND max_range_weight")
            ->first();

        if (isset($int_delivery_cost)) {
            $total_cost = $int_delivery_cost->base_cost_per_kg * $weight;
            $this->base_cost = $int_delivery_cost->base_cost_per_kg * $weight;
            $this->profit = $this->base_cost * ($int_delivery_cost->profit_percentage / 100);
            $total_cost =  $this->base_cost + $this->profit;



            $int_add_delivery_cost = IntAddDeliveryCost::where('int_delivery_route_code', $route_code)
                ->where('goods_type_code', $goods_type_code)
                ->first();
            $this->goods_type_profit_share = false;
            if (isset($int_add_delivery_cost)) {
                $this->add_cost = $int_add_delivery_cost->add_cost_per_kg * $weight;
                $total_cost = $total_cost + $this->add_cost;
                $this->add_cost_per_kg = $int_add_delivery_cost->add_cost_per_kg;
                $this->goods_type_profit_share = $int_add_delivery_cost->goods_type
                    ->profit_share;
            } else {
                $this->add_cost_per_kg = 0;
            }


            if ($user->hasPermission('is_int_checker')) {
            } else {
                if (isset($branch)) {
                    $fee_percentage = $branch->int_fee_percentage;
                }
                $this->fee_percentage = $fee_percentage;
            }


            $this->total_cost = $total_cost;
            $this->original_total_cost = $this->total_cost;

            if ($this->goods_type_profit_share) {
                $this->fee = $this->total_cost * ($this->fee_percentage / 100);
            } else {
                $this->fee = ($this->total_cost - $this->add_cost)
                    * ($this->fee_percentage / 100);
            }

            $this->net_profit = $this->profit - $this->fee;



            $this->int_delivery_route_code = $int_delivery_cost->int_delivery_route_code;
            $this->min_range_weight = $int_delivery_cost->min_range_weight;
            $this->max_range_weight = $int_delivery_cost->max_range_weight;
            $this->base_cost_per_kg = $int_delivery_cost->base_cost_per_kg;
            $this->profit_percentage = $int_delivery_cost->profit_percentage;

            if ($user->hasPermission('is_int_checker')) {
                $this->checker_total_cost =  $total_cost - $this->fee;
                $this->different_total_cost = $this->branch_total_cost - $this->checker_total_cost;
            } else {
                $this->branch_total_cost = $total_cost - $this->fee;
            }
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
            $this->goods_weight &&

            is_numeric($this->goods_height) &&
            is_numeric($this->goods_width) &&
            is_numeric($this->goods_length) &&
            is_numeric($this->goods_weight)
        ) {
            $this->initWeight();
        } else {
            $this->goods_volume_weight = 0;
        }

        if (
            isset($this->origin_region) &&
            isset($this->consignee_region) &&
            isset($this->goods_type) &&

            is_numeric($this->goods_weight) &&
            is_numeric($this->goods_volume_weight) &&

            $this->goods_weight &&
            $this->goods_volume_weight

        ) {
            $this->initTotalCost();

            if ($this->total_cost) {
                $fields->total_cost->value = $this->total_cost;
                $fields->fee->value = $this->fee;
            } else {
                Flash::warning(e(trans('kju.express::lang.global.service_not_available')));
                $fields->total_cost->value = 0;
                $fields->fee->value = 0;
            }
        } else {
            $this->total_cost = 0;
            $fields->total_cost->value = 0;
            $fields->fee->value = 0;
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
        }

        // SET INIT STATUS
        $this->status = 'pending';
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

        if ($user->hasPermission('is_int_checker') && $this->status == 'pending') {
            $order_status = new IntDeliveryOrderStatus();
            $order_status->region = $this->origin_region;
            $order_status->status = $this->getOriginalPurgeValue('checker_action');
            $order_status->created_user = $this->created_user;
            $order_status->int_delivery_order_code = $this->code;
            $order_status->save();

            $this->status = $order_status->status;
        }
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


    public function afterCreate()
    {
        $order_status = new IntDeliveryOrderStatus();
        $order_status->region = $this->origin_region;
        $order_status->status = $this->status;
        $order_status->created_user = $this->created_user;
        $order_status->int_delivery_order_code = $this->code;
        $order_status->save();
    }

    public function getDisplayStatusAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->status));
    }

    public function getDisplayPaymentStatusAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->payment_status));
    }

    public function getDisplayPaymentMethodAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->payment_method));
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
