<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Carbon\Carbon;
use Exception;
use Kju\Express\Classes\IdGenerator;
use Kju\Express\Facades\BalanceHelper;
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

    protected $purgeable = ['total_cost_agreement', 'checker_action', 'tracker_action'];

    protected $dates = ['deleted_at', 'process_at', 'received_at'];

    protected $revisionable = [
        'customer_id', 'branch_id', 'origin_region_id', 'consignee_region_id',
        'consignee_name', 'consignee_phone_number', 'consignee_address',
        'consignee_postal_code', 'goods_description', 'goods_amount', 'goods_weight',
        'goods_volume_weight', 'goods_height', 'goods_width', 'goods_length', 'original_total_cost', 'base_cost',
        'add_cost', 'total_cost', 'branch_total_cost', 'checker_total_cost', 'different_total_cost',
        'checker_comment', 'tracking_number', 'profit',
        'net_total_cost', 'net_profit', 'goods_type_profit_share',
        'fee', 'fee_percentage', 'payment_method', 'status',
        'int_delivery_route_id', 'min_range_weight', 'max_range_weight',
        'base_cost_per_kg', 'profit_percentage', 'add_cost_per_kg', 'goods_type_id',
        'created_at', 'updated_at', 'deleted_at', 'export_at',
        'received_at', 'updated_user_id', 'created_user_id', 'deleted_user_id',
        'vendor_id'
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
        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_id'],
        'origin_region' => ['Kju\Express\Models\Region', 'key' => 'origin_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region', 'key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer', 'key' => 'customer_id'],

        'goods_type' => ['Kju\Express\Models\GoodsType'],

        'updated_user' => ['Kju\Express\Models\User', 'key' => 'updated_user_id'],
        'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
        'deleted_user' => ['Kju\Express\Models\User', 'key' => 'deleted_user_id'],

        'vendor' => ['Kju\Express\Models\Vendor', 'key' => 'vendor_id'],
        'balance' => ['Kju\Express\Models\Balance', 'key' => 'balance_id'],

    ];

    public $hasMany = [
        'statuses' => ['Kju\Express\Models\IntDeliveryOrderStatus']
    ];

    public function getRevisionableUser()
    {
        return BackendAuth::getUser();
    }

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
        $branch = $user->branch;

        // if (empty($this->status)) {
        if (isset($branch)) {
            $this->fee_percentage = $branch->int_fee_percentage;
        }

        if ($this->goods_weight > $this->goods_volume_weight) {
            $this->goods_ceil_weight = ceil($this->goods_weight);
        } else {
            $this->goods_ceil_weight = ceil($this->goods_volume_weight);
        }
        $this->int_delivery_route_code = $this->origin_region->id . '-' . $this->consignee_region->id;

        $route = IntDeliveryRoute::where('code', $this->int_delivery_route_code)->first();

        $int_delivery_cost = IntDeliveryCost::where('int_delivery_route_id', $route->id)
            ->whereRaw("$this->goods_ceil_weight  BETWEEN min_range_weight AND max_range_weight")
            ->first();


        if (empty($int_delivery_cost)) {
            return;
        }
        $this->min_range_weight = $int_delivery_cost->min_range_weight;
        $this->max_range_weight = $int_delivery_cost->max_range_weight;
        $this->base_cost_per_kg = $int_delivery_cost->base_cost_per_kg;
        $this->profit_percentage = $int_delivery_cost->profit_percentage;

        $int_add_delivery_cost = IntAddDeliveryCost::where('int_delivery_route_id', $route->id)
            ->where('goods_type_id', $this->goods_type->id)
            ->first();

        if (isset($int_add_delivery_cost)) {
            $this->add_cost_per_kg = $int_add_delivery_cost->add_cost_per_kg;
            $this->add_cost = $int_add_delivery_cost->add_cost_per_kg * $this->goods_ceil_weight;
            $this->goods_type_profit_share = $int_add_delivery_cost->goods_type
                ->profit_share;
        } else {
            $this->add_cost_per_kg = 0;
            $this->add_cost = 0;
            $this->goods_type_profit_share = false;
        }
        // }

        $this->base_cost = $this->base_cost_per_kg  * $this->goods_ceil_weight;
        $this->profit = $this->base_cost * ($this->profit_percentage / 100);


        $this->total_cost =  $this->base_cost +  $this->profit;

        $this->total_cost =  $this->total_cost + $this->add_cost;
        $this->original_total_cost = $this->total_cost;


        if ($this->goods_type_profit_share) {
            $this->fee = $this->total_cost * ($this->fee_percentage / 100);
        } else {
            $this->fee = ($this->total_cost - $this->add_cost)
                * ($this->fee_percentage / 100);
        }

        $this->net_profit = $this->profit - $this->fee;
        $this->net_total_cost = $this->total_cost - $this->fee;


        if ($user->hasPermission('is_checker')) {
            $this->checker_total_cost =  $this->total_cost - $this->fee;
            $this->different_total_cost = $this->branch_total_cost - $this->checker_total_cost;
        } else {
            $this->branch_total_cost = $this->total_cost - $this->fee;
        }
    }


    public function filterFields($fields, $context = null)
    {
        $user = BackendAuth::getUser();
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

                if ($user->hasPermission('is_checker')) {
                    $fields->checker_total_cost->value = $this->checker_total_cost;
                    $fields->different_total_cost->value = $this->different_total_cost;
                }
            } else {
                Flash::warning(e(trans('kju.express::lang.global.service_not_available')));
                $fields->total_cost->value = 0;
                $fields->fee->value = 0;
                if ($user->hasPermission('is_checker')) {
                    $fields->checker_total_cost->value = 0;
                    $fields->different_total_cost->value = 0;
                }
            }
        } else {
            $this->total_cost = 0;
            $fields->total_cost->value = 0;
            $fields->fee->value = 0;

            if ($user->hasPermission('is_checker')) {
                $fields->checker_total_cost->value = 0;
                $fields->different_total_cost->value = 0;
            }
        }
    }

    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;

        //INITIALIZE CODE
        $code = IdGenerator::alpha(isset($this->branch) ? $this->branch->id : '', 3)
            . $this->origin_region->id
            . IdGenerator::numeric(isset($this->branch) ? $this->branch->id : '', 4);
        $this->code = $code;

        $this->created_user = $user;

        //SET BRANCH & BRANCH REGION
        if (isset($branch)) {
            $this->branch = $branch;
        }

        $balance = BalanceHelper::getMyBalance();
        $this->balance_id = $balance->id;

        // SET INIT STATUS
        $this->status = 'pending';
        $this->process_at = Carbon::now();
    }

    public function beforeSave()
    {
        if (empty($this->status) || $this->status == 'pending') {
            $this->initWeight();
            $this->initTotalCost();

            if (!$this->total_cost) {
                throw new ApplicationException(e(trans('kju.express::lang.global.service_not_available')));
            }
        }
    }

    public function beforeUpdate()
    {
        $user = BackendAuth::getUser();
        $this->updated_user = $user;

        if ($user->hasPermission('is_checker') && $this->status == 'pending') {
            $order_status = new IntDeliveryOrderStatus();
            $order_status->region = $this->origin_region;
            $order_status->status = $this->getOriginalPurgeValue('checker_action');
            $order_status->created_user = $this->created_user;
            $order_status->int_delivery_order_id = $this->id;
            $order_status->save();

            $this->status = $order_status->status;
        }


        if ($user->hasPermission('is_tracker') && $this->status == 'process') {
            $order_status = new IntDeliveryOrderStatus();
            $order_status->region = $this->origin_region;
            $order_status->status = $this->getOriginalPurgeValue('tracker_action');
            $order_status->created_user = $this->created_user;
            $order_status->int_delivery_order_id = $this->id;
            $order_status->save();

            $this->status = $order_status->status;
        }
    }

    public function beforeDelete()
    {
        $user = BackendAuth::getUser();
        if ($user->isSuperUser()) {
            // Nothing
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
        $order_status->int_delivery_order_id = $this->id;
        $order_status->save();


        BalanceHelper::creditMyBalance($this->fee, "INTERNATIONAL_ORDER_FEE:$this->code");
        BalanceHelper::debitMyBalance($this->total_cost, "INTERNATIONAL_ORDER:$this->code");
    }

    public function afterUpdate()
    {
        if ($this->status == 'process') {
            if ($this->different_total_cost > 0) {
                BalanceHelper::creditBalance($this->balance_id, $this->different_total_cost, "DIFF_INTERNATIONAL_ORDER:$this->code");
            }

            if ($this->different_total_cost < 0) {
                BalanceHelper::debitBalance($this->balance_id, $this->different_total_cost, "DIFF_INTERNATIONAL_ORDER:$this->code");
            }
        }
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
