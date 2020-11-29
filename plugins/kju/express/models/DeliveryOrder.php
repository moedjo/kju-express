<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\DB;
use Kju\Express\classes\IdGenerator;
use Model;
use October\Rain\Exception\ApplicationException;

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

    protected $purgeable = ['agreement'];


    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'branch' => 'required',
        'branch_region' => 'required',

        // 'customer' => 'required',

        'consignee_region' => 'required',
        'consignee_name' => [
            'required',
            'regex:/^[\pL\s\-]+$/u',
        ],
        'consignee_address' => 'required',
        'consignee_phone_number' => [
            'required',
            'regex:/(?:\+62)?0?8\d{2}(\d{8})/',
        ],

        'consignee_postal_code' => 'required|digits:5',

        'service' => 'required',

        'goods_weight' => 'required',
        'goods_amount' => 'required',



        // Purgeable Field
        'agreement' => 'in:1',
    ];
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

    public function beforeValidate()
    {

        // we need to check record is created or not
        if ($this->code == null) {
            // CREATE CASE
            // we need to use differ binding scope as this record is not saved yet.
            if ($this->customer()->withDeferred(post('_session_key'))->count() == 0) {
                // throw new \ValidationException(['customer' => 'We need User !']);
                $this->rules['customer'] = "required";
                $this->setValidationAttributeName('customer', 'kju.express::lang.customer.singular');
            }

            if ($this->pickup_request) {
                $this->rules['pickup_region'] = "required";
                $this->rules['pickup_courier'] = "required";
                $this->rules['pickup_date'] = "required|after:" . date('Y-m-d') . "|before:" . date("Y-m-d", strtotime("+1 week"));

                $this->rules['pickup_address'] = "required";
                $this->rules['pickup_postal_code'] = "required|digits:5";
                $this->rules['branch_region'] = "required|in:" . substr($this->pickup_region->id, 0, 4);
            }

            if (isset($this->service) && $this->service->weight_limit != -1) {
                $this->rules['goods_weight'] = "required";
            }
            $this->rules['total_cost'] = "required|min:1";
        }
    }

    public function beforeSave()
    {
        $user = BackendAuth::getUser();
        $this->updated_user = $user;
    }

    public function beforeCreate()
    {

        //INITIALIZE USER & BRANCH
        $user = BackendAuth::getUser();
        $branch = $user->branch;

        $this->created_user = $user;

        // CODE GENERATOR
        $config = [
            'table' => $this->table,
            'field' => $this->primaryKey,
            'length' => 10,
            'prefix' => 'KJU'
        ];
        $code = IdGenerator::generate($config);
        $this->code = $code;


        //SET BRANCH & BRANCH REGION
        if (!$user->isSuperUser()) {
            if (isset($branch)) {
                $this->branch = $branch;
                $this->branch_region = $branch->region;
            }
        }

        // SET INIT STATUS
        if ($this->pickup_request) {
            $this->status = 'pickup';
        } else {
            $this->status = 'process';
        }

        // initialize total cost
        $origin_id = null;
        $destination_id = $this->consignee_region->id;
        $service_code = $this->service->code;

        if ($this->pickup_request) {
            $origin_id = $this->pickup_region->id;
        } else {
            $origin_id = $this->branch_region->id;
        }
        $cost_id = $this->getCostId($service_code, $origin_id, $destination_id);
        if (isset($cost_id)) {
            $this->calculateTotalCost($cost_id);
        }
    }

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

    private function calculateTotalCost($cost_id)
    {
        $cost = DeliveryCost::find($cost_id);

        $this->cost = $cost->cost;
        $this->add_cost = $cost->add_cost;
        $this->weight_limit = $cost->service->weight_limit;
        $this->delivery_route_code = $cost->delivery_route->code;
        $this->min_lead_time = $cost->min_lead_time;
        $this->max_lead_time = $cost->max_lead_time;

        if ($cost->service->weight_limit == -1) {
            $this->total_cost = $cost->cost;
        } else {
            $add_cost = ($this->goods_weight - $cost->service->weight_limit) * $cost->add_cost;
            $add_cost = $add_cost < 0 ? 0 : $add_cost;
            $this->total_cost  = $add_cost + $cost->cost;
        }
    }

    public function filterFields($fields, $context = null)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;

        if (isset($this->service)) {
            if ($this->service->weight_limit == -1) {
                $fields->goods_weight->hidden = true;
            } else {
                $fields->goods_weight->hidden = false;
            }
        } else {
            $fields->goods_weight->hidden = true;
        }

        if ($context == 'update') {
            $fields->branch->disabled = true;
            $fields->branch_region->disabled = true;

            $fields->customer->disabled = true;

            $fields->consignee_region->disabled = true;
            $fields->consignee_name->disabled = true;
            $fields->consignee_phone_number->disabled = true;
            $fields->consignee_address->disabled = true;
            $fields->consignee_postal_code->disabled = true;

            $fields->pickup_request->disabled = true;
            $fields->service->readOnly = true;
            $fields->goods_weight->disabled = true;
            $fields->goods_description->disabled = true;
            $fields->goods_amount->disabled = true;


            $fields->pickup_region->disabled = true;
            $fields->pickup_courier->disabled = true;
            $fields->pickup_region->disabled = true;
            $fields->pickup_date->disabled = true;
            $fields->pickup_address->disabled = true;
            $fields->pickup_postal_code->disabled = true;

            if ($this->status == 'pickup') {
                $fields->service->readOnly = false;
                $fields->goods_weight->disabled = false;
                $fields->goods_description->disabled = false;
                $fields->goods_amount->disabled = false;

                $fields->pickup_courier->disabled = false;
                $fields->pickup_date->disabled = false;
            }
        }


        if ($context == 'create') {


            if (!$user->isSuperUser()) {
                $fields->branch->readOnly = true;
                $fields->branch_region->readOnly = true;
            }

            if (empty($this->branch_region)) {
                return false;
            }
            if (empty($this->service)) {
                return false;
            }
            if (empty($this->consignee_region)) {
                return false;
            }

            if ($this->pickup_request) {
                if (empty($this->pickup_region)) {
                    return false;
                }
            }

            // initialize total cost
            $origin_id = null;
            $destination_id = $this->consignee_region->id;
            $service_code = $this->service->code;

            if ($this->pickup_request) {
                $origin_id = $this->pickup_region->id;
            } else {
                $origin_id = $this->branch_region->id;
            }

            $cost_id = $this->getCostId($service_code, $origin_id, $destination_id);

            if (isset($cost_id)) {
                $this->calculateTotalCost($cost_id);
                $fields->total_cost->value = $this->total_cost;
            }
        }
    }


    public function beforeDelete()
    {
        throw new ApplicationException("You cannot delete me!");
    }
}