<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Kju\Express\classes\IdGenerator;
use Model;
use October\Rain\Exception\ApplicationException;
use October\Rain\Support\Facades\Flash;

/**
 * Model
 */
class DeliveryOrder extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Purgeable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_orders';
    protected $primaryKey = 'code';
    public $incrementing = false;

    protected $purgeable = ['agreement', 'discount_agreement'];


    protected $dates = ['deleted_at', 'process_at', 'received_at', 'pickup_date'];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $attributeNames = [];

    public $belongsTo = [

        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_code'],
        'branch_region' => ['Kju\Express\Models\Region', 'key' => 'branch_region_id'],
        'pickup_region' => ['Kju\Express\Models\Region', 'key' => 'pickup_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region', 'key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer', 'key' => 'customer_id'],
        'pickup_courier' => ['Kju\Express\Models\User', 'key' => 'pickup_courier_user_id'],
        'service' => ['Kju\Express\Models\Service', 'key' => 'service_code'],

        'updated_user' => ['Kju\Express\Models\User', 'key' => 'updated_user_id'],
        'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
        'deleted_user' => ['Kju\Express\Models\User', 'key' => 'deleted_user_id'],

    ];

    public $hasMany = [
        'statuses' => ['Kju\Express\Models\DeliveryOrderStatus']
    ];

    private function getCostId($service_code, $origin_id, $destination_id)
    {

        $src_regency_id = substr($origin_id, 0, 4);
        $dst_regency_id = substr($destination_id, 0, 4);
        $cost = DB::table('kju_express_delivery_costs AS cost')
            ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_code', '=', 'route.code')
            ->select('cost.id', 'cost.cost', 'cost.add_cost')
            ->where('cost.service_code', $service_code)
            ->where('route.src_region_id', $src_regency_id)
            ->whereIn('route.dst_region_id', [$destination_id, $dst_regency_id])
            ->orderByRaw("FIELD(route.dst_region_id,'$destination_id','$dst_regency_id')")
            ->first();

        return isset($cost) ? $cost->id : null;
    }
 
    private function initData($cost_id)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;

        $cost = DeliveryCost::findOrFail($cost_id);

        $this->cost = $cost->cost;
        $this->add_cost = $cost->add_cost;
        $this->weight_limit = $cost->service->weight_limit;
        $this->delivery_route_code = $cost->route->code;
        $this->min_lead_time = $cost->min_lead_time;
        $this->max_lead_time = $cost->max_lead_time;

        if ($this->pickup_request) {
            $this->payment_status = 'paid';
            $this->payment_description = '';

            $this->discount = 0;
        }

        if ($cost->service->weight_limit == -1) {
            $this->total_cost = $cost->cost;
            $this->original_total_cost = $this->total_cost + 0;
            $this->goods_amount = 1;
            $total_discount =  $this->total_cost * ($this->discount / 100);
            $this->total_cost = $this->total_cost - $total_discount;

            if(isset($branch)){
                $this->fee_percentage = $branch->dom_fee_percentage;
                $this->fee = $this->total_cost * ($this->fee_percentage / 100);
                $this->branch_total_cost = $this->total_cost - $this->fee;
            }

        } else {

            $add_cost = (ceil($this->goods_weight) - $cost->service->weight_limit) * $cost->add_cost;
            $add_cost = $add_cost < 0 ? 0 : $add_cost;
            $this->total_cost = $add_cost + $cost->cost;
            $this->original_total_cost = $this->total_cost + 0;
            $total_discount =  $this->total_cost * ($this->discount / 100);
            $this->total_cost = $this->total_cost - $total_discount;

            if(isset($branch)){
                $this->fee_percentage = $branch->dom_fee_percentage;
                $this->fee = $this->total_cost * ($this->fee_percentage / 100);
                $this->branch_total_cost = $this->total_cost - $this->fee;
            }
        }

        if ($user->hasPermission('is_courier')) {
            if ($this->status == 'pickup') {
                $this->status = 'process';
            }
        }
    }

    public function filterFields($fields, $context = null)
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;


        if (isset($this->service)) {
            if ($this->service->weight_limit == -1) {
                $fields->goods_weight->hidden = true;
                $fields->goods_amount->hidden = true;
            } else {
                $fields->goods_weight->hidden = false;
                $fields->goods_amount->hidden = false;
                if (empty($this->goods_weight)) {
                    return;
                }
            }
        } else {
            $fields->goods_weight->hidden = true;
            $fields->goods_amount->hidden = true;
        }


        if (isset($branch)) {
            $this->branch_region = $branch->region;
        } else if (isset($this->branch_region)) {
            // Nothing
        } else {
            return false;
        }

        if (empty($this->service)) {
            return false;
        }
        if (empty($this->consignee_region)) {
            return false;
        }

        if (isset($this->discount)) {
            if (!is_numeric($this->discount)) {
                return false;
            }
        }

        $service_code = $this->service->code;
        $origin_id = $this->branch_region->id;
        $destination_id = $this->consignee_region->id;
        $cost_id = $this->getCostId($service_code, $origin_id, $destination_id);
        if (isset($cost_id)) {
            $this->initData($cost_id);
            $fields->total_cost->value = $this->total_cost;
            $fields->fee->value = $this->fee;

        } else {
            Flash::warning(e(trans('kju.express::lang.global.service_not_available')));
            $fields->total_cost->value = 0;
            $fields->fee->value = 0;
        }
    }

    public function beforeValidate()
    {
    }




    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;

        //INITIALIZE CODE
        $code = IdGenerator::alpha(isset($this->branch)?$this->branch->code:'', 4)
            . $this->branch_region->id
            . IdGenerator::numeric(isset($this->branch)?$this->branch->code:'', 4);

        $this->code = $code;

        $this->created_user = $user;
        //SET BRANCH & BRANCH REGION
        if (isset($branch)) {
            $this->branch = $branch;
            $this->branch_region = $branch->region;
        }

        // SET INIT STATUS
        if ($this->pickup_request) {
            $this->status = 'pickup';
        } else {
            $this->status = 'process';
            $this->process_at = Carbon::now();
        }
    }

    public function beforeSave()
    {
        $origin_id = $this->branch_region->id;
        $destination_id = $this->consignee_region->id;
        $service_code = $this->service->code;

        $cost_id = $this->getCostId($service_code, $origin_id, $destination_id);
        trace_log($cost_id);
        if (isset($cost_id)) {
            $this->initData($cost_id);
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
            if ($this->status == 'pickup' || $this->status == 'process') {
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
        $user = BackendAuth::getUser();
        if ($user->hasPermission('is_courier')) {
            if ($this->original['status'] == 'pickup' && $this->status == 'process') {
                $order_status = new DeliveryOrderStatus();
                $order_status->region = $this->branch_region;
                $order_status->status = $this->status;
                $order_status->created_user = $this->created_user;
                $order_status->delivery_order_code = $this->code;

                $order_status->save();
            }
        }
    }


    public function afterCreate()
    {
        $order_status = new DeliveryOrderStatus();
        $order_status->region = $this->branch_region;
        $order_status->status = $this->status;
        $order_status->created_user = $this->created_user;
        $order_status->delivery_order_code = $this->code;
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
}
