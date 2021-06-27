<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Kju\Express\Classes\IdGenerator;
use Kju\Express\Facades\BalanceHelper;
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
    use \October\Rain\Database\Traits\Revisionable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_orders';

    protected $purgeable = ['agreement', 'discount_agreement'];


    protected $dates = ['deleted_at', 'process_at', 'received_at', 'pickup_date'];

    protected $revisionable = [
        'customer_id', 'branch_id', 'branch_region_id',
        'pickup_region_id', 'pickup_courier_user_id', 'pickup_request', 'pickup_date',
        'pickup_address', 'pickup_postal_code', 'consignee_region_id', 'consignee_name',
        'consignee_phone_number', 'consignee_address', 'consignee_postal_code',
        'service_id', 'goods_description', 'goods_amount', 'goods_weight',
        'total_cost', 'fee', 'fee_percentage', 'original_total_cost', 'net_total_cost',
        'discount', 'payment_status', 'payment_description', 'status',
        'delivery_route_code', 'cost', 'add_cost', 'weight_limit', 'min_lead_time', 'max_lead_time',
        'created_at', 'updated_at', 'deleted_at', 'process_at', 'received_at',
        'updated_user_id', 'created_user_id', 'deleted_user_id'
    ];
    public $morphMany = [
        'revision_history' => ['System\Models\Revision', 'name' => 'revisionable']
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $attributeNames = [];

    public $belongsTo = [

        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_id'],
        'branch_region' => ['Kju\Express\Models\Region', 'key' => 'branch_region_id'],
        'pickup_region' => ['Kju\Express\Models\Region', 'key' => 'pickup_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region', 'key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer', 'key' => 'customer_id'],
        'pickup_courier' => ['Kju\Express\Models\User', 'key' => 'pickup_courier_user_id'],
        'service' => ['Kju\Express\Models\Service', 'key' => 'service_id'],

        'updated_user' => ['Kju\Express\Models\User', 'key' => 'updated_user_id'],
        'created_user' => ['Kju\Express\Models\User', 'key' => 'created_user_id'],
        'deleted_user' => ['Kju\Express\Models\User', 'key' => 'deleted_user_id'],

        'balance' => ['Kju\Express\Models\Balance', 'key' => 'balance_id'],

    ];

    public $hasMany = [
        'statuses' => ['Kju\Express\Models\DeliveryOrderStatus']
    ];

    public function getRevisionableUser()
    {
        return BackendAuth::getUser();
    }

    private function initTotalCost()
    {

        if (empty($this->status)) {

            $service_id = $this->service->id;
            $origin_id = $this->branch_region->id;
            $destination_id = $this->consignee_region->id;

            $src_regency_id = substr($origin_id, 0, 4);
            $dst_regency_id = substr($destination_id, 0, 4);
            $cost = DB::table('kju_express_delivery_costs AS cost')
                ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_id', '=', 'route.id')
                ->select('cost.id', 'cost.cost', 'cost.add_cost')
                ->where('cost.service_id', $service_id)
                ->where('route.src_region_id', $src_regency_id)
                ->whereIn('route.dst_region_id', [$destination_id, $dst_regency_id])
                ->orderByRaw("FIELD(route.dst_region_id,'$destination_id','$dst_regency_id')")
                ->first();


            if (empty($cost)) {
                return;
            }

            $cost = DeliveryCost::findOrFail($cost->id);

            $this->cost = $cost->cost;
            $this->add_cost = $cost->add_cost;
            $this->weight_limit = $cost->service->weight_limit;
            $this->delivery_route_code = $cost->route->code;
            $this->min_lead_time = $cost->min_lead_time;
            $this->max_lead_time = $cost->max_lead_time;
            $this->branch = BackendAuth::getUser()->branch;
        }

        if ($this->weight_limit == -1) {
            $this->total_cost =  $this->cost;
            $this->original_total_cost = $this->total_cost + 0;
            $this->goods_amount = 1;
        } else {
            $add_cost = (ceil($this->goods_weight) - $this->weight_limit) * $this->add_cost;
            $add_cost = $add_cost < 0 ? 0 : $add_cost;
            $this->total_cost = $add_cost + $this->cost;
            $this->original_total_cost = $this->total_cost + 0;
        }

        if (is_numeric($this->discount) && $this->discount > 0) {
            $total_discount =  $this->total_cost * ($this->discount / 100);
            $this->total_cost = $this->total_cost - $total_discount;
            $this->net_total_cost = $this->total_cost;
        }

        if (isset($this->branch)) {
            $this->fee_percentage = $this->branch->dom_fee_percentage;
            $this->fee = $this->total_cost * ($this->fee_percentage / 100);
            $this->net_total_cost = $this->total_cost - $this->fee;
        } else {
            $this->fee_percentage = 0;
            $this->fee = 0;
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
        }

        if (
            isset($this->branch_region) &&
            isset($this->consignee_region) &&
            isset($this->service)
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
        }
    }

    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;

        //INITIALIZE CODE
        $code = IdGenerator::alpha(isset($this->branch) ? $this->branch->id : '', 4)
            . $this->branch_region->id
            . IdGenerator::numeric(isset($this->branch) ? $this->branch->id : '', 4);

        $this->code = $code;


        //SET BRANCH & BRANCH REGION
        if (isset($branch)) {
            $this->branch = $branch;
            $this->branch_region = $branch->region;
        }

        $balance = BalanceHelper::getMyBalance();
        $this->balance_id = $balance->id;

        $this->status = 'process';
        $this->process_at = Carbon::now();
        $this->created_user = $user;
    }

    public function beforeSave()
    {
        $this->initTotalCost();
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
       
    }

    public function afterCreate()
    {
        $order_status = new DeliveryOrderStatus();
        $order_status->region = $this->branch_region;
        $order_status->status = $this->status;
        $order_status->created_user = $this->created_user;
        $order_status->delivery_order_id = $this->id;
        $order_status->save();

        BalanceHelper::creditMyBalance($this->fee,"DOMESTIC_ORDER_FEE:$this->code");
        BalanceHelper::debitMyBalance($this->total_cost,"DOMESTIC_ORDER:$this->code");
    }

    public function getDisplayStatusAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->status));
    }

    public function getDisplayPaymentStatusAttribute()
    {
        return e(trans('kju.express::lang.global.' . $this->payment_status));
    }

    public function getStatusOptions()
    {
        return DeliveryOrderStatus::getStatusOptions();
    }
}
