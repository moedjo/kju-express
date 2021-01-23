<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Exception;
use Model;
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

        $weight = (
            $height *
            $width *
            $length
        ) / self::VOLUME_DIVIDER;

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
            }

            $this->total_cost = $total_cost;
        } else {
            $this->total_cost = null;
        }
    }


    public function filterFields($fields, $context = null)
    {


        if (
            $this->goods_height &&
            $this->goods_width &&
            $this->goods_length
        ) {
            $this->initWeight();
            $fields->goods_weight->value = $this->goods_weight;
        } else{
            $this->goods_weight = 0;
            $fields->goods_weight->value = 0;
        }

        if (
            isset($this->origin_region) &&
            isset($this->consignee_region) &&
            isset($this->goods_type) &&
            $this->goods_weight
        ) {

            $this->initTotalCost();

            if ($this->total_cost) {
                $fields->total_cost->value = $this->total_cost;
            } else {
                Flash::warning(e(trans('kju.express::lang.global.service_not_available')));
                $fields->total_cost->value = 0;
            }
        }else {
            $this->total_cost = 0;
            $fields->total_cost->value = 0;
        }
    }
}
