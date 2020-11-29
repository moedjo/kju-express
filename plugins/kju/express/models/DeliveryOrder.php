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

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kju_express_delivery_orders';
    protected $primaryKey = 'code';
    public $incrementing = false;

 
    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'branch' => 'required',
        'branch_region' => 'required',
        'consignee_region' => 'required',
        'consignee_phone_number' => 'required',
        'consignee_address' => 'required',
        'consignee_postal_code' => 'required',
        'service' => 'required',
     
    ];

    public $belongsTo = [
        'branch_region' => ['Kju\Express\Models\Region', 'key' => 'branch_region_id'],
        'pickup_region' => ['Kju\Express\Models\Region', 'key' => 'pickup_region_id'],
        'consignee_region' => ['Kju\Express\Models\Region', 'key' => 'consignee_region_id'],

        'customer' => ['Kju\Express\Models\Customer', 'key' => 'customer_id'],
        'pickup_courier' => ['Kju\Express\Models\User', 'key' => 'pickup_courier_user_id'],
        'service' => ['Kju\Express\Models\Service', 'key' => 'service_code'],

        'branch' => ['Kju\Express\Models\Branch', 'key' => 'branch_code'],

        'updated_user' => ['Kju\Express\Models\User', 'key' => 'updated_user_id'],
    ];


    public function beforeValidate()
    {
        if ($this->pickup_request) {
            $this->rules['pickup_courier'] = "required";
            $this->rules['pickup_date'] = "required";
            $this->rules['pickup_region'] = "required";
            $this->rules['pickup_address'] = "required";
            $this->rules['pickup_postal_code'] = "required";

            $this->rules['branch_region'] = "required|string:".substr($this->pickup_region->id, 0, 4);
        }

        if (isset($this->service) && $this->service->weight_limit != -1) {
            $this->rules['goods_weight'] = "required";
        }
        // $this->rules['total_cost'] = "required";
    }

    

    public function beforeCreate()
    {

        //INITIALIZE USER & BRANCH
        $user = BackendAuth::getUser();
        $branch = $user->branch;

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
           if(isset($branch)){
               $this->branch = $branch;
               $this->branch_region = $branch->region;
           }
        }

        // SET INIT STATUS
        if ($this->pickup_request) {
            $this->status = 'pick_up';
        } else {
            $this->status = 'process';
        }

        $origin_id = null;
        $destination_id = $this->consignee_region->id;
        $service = $this->service;
        if ($this->pickup_request) {
            $origin_id = $this->pickup_region->id;
        } else {
            $origin_id = $this->branch_region->id;
        }

        $src_regency_id = substr($origin_id, 0, 4);
        $dst_regency_id = substr($destination_id, 0, 4);
        $cost = DB::table('kju_express_delivery_costs AS cost')
            ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_code', '=', 'route.code')
            ->select('cost.id', 'cost.cost', 'cost.add_cost')
            ->where('cost.service_code', $service->code)
            ->where('route.src_region_id', $src_regency_id)
            ->whereIn('route.dst_region_id', [$destination_id, $dst_regency_id])
            ->orderByRaw("FIELD(route.dst_region_id,'$destination_id','$dst_regency_id')")
            ->first();


        if (isset($cost)) {
            $cost = DeliveryCost::find($cost->id);
            $this->cost = $cost->cost;
            $this->add_cost = $cost->add_cost;
            $this->weight_limit = $this->service->weight_limit;
            $this->delivery_route_code = $cost->delivery_route->code;

            $this->min_lead_time = $cost->min_lead_time;
            $this->max_lead_time = $cost->max_lead_time;
            if ($service->weight_limit == -1) {
                $this->total_cost = $cost->cost;
            } else {
                $add_cost = ($this->goods_weight - $service->weight_limit) * $cost->add_cost;
                $add_cost = $add_cost < 0 ? 0 : $add_cost;
                $this->total_cost  = $add_cost + $cost->cost;
            }
        }
    }

    private function getCost($service_code,$origin_id,$destination_id){

        return null;
    }
    private function calculateTotalCost(DeliveryCost $cost){
        
    }

    public function filterFields($fields, $context = null)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;
        
        if ($context == 'update') {
            $fields->branch->readOnly = true;
            $fields->branch_region->readOnly = true;

            $fields->consignee_region->readOnly = true;
            $fields->consignee_phone_number->readOnly = true;
            $fields->consignee_address->readOnly = true;
            $fields->consignee_postal_code->readOnly = true;

            $fields->pickup_request->disabled = true;

            // TODO bro
            $fields->service->readOnly = true;
        }

        if (isset($this->service)) {
            if ($this->service->weight_limit == -1) {
                $fields->goods_weight->hidden = true;
            } else {
                $fields->goods_weight->hidden = false;
            }
        } else {
            $fields->goods_weight->hidden = true;
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

            $origin_id = null;
            $destination_id = $this->consignee_region->id;
            $service = $this->service;
            if ($this->pickup_request) {
                if (empty($this->pickup_region)) {
                    return false;
                }
                $origin_id = $this->pickup_region->id;
            } else {
                $origin_id = $this->branch_region->id;
            }

            $src_regency_id = substr($origin_id, 0, 4);
            $dst_regency_id = substr($destination_id, 0, 4);
            $cost = DB::table('kju_express_delivery_costs AS cost')
                ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_code', '=', 'route.code')
                ->select('cost.id', 'cost.cost', 'cost.add_cost')
                ->where('cost.service_code', $service->code)
                ->where('route.src_region_id', $src_regency_id)
                ->whereIn('route.dst_region_id', [$destination_id, $dst_regency_id])
                ->orderByRaw("FIELD(route.dst_region_id,'$destination_id','$dst_regency_id')")
                ->first();

            if (isset($cost)) {
                $cost = DeliveryCost::find($cost->id);
                $this->cost = $cost->cost;
                $this->add_cost = $cost->add_cost;
                $this->weight_limit = $cost->weight_limit;
                $this->min_lead_time = $cost->min_lead_time;
                $this->max_lead_time = $cost->max_lead_time;
                if ($service->weight_limit == -1) {
                    $this->total_cost = $cost->cost;
                } else {
                    $add_cost = ($this->goods_weight - $service->weight_limit) * $cost->add_cost;
                    $add_cost = $add_cost < 0 ? 0 : $add_cost;
                    $this->total_cost  = $add_cost + $cost->cost;
                }
            }
        }
    }


    public function beforeDelete(){
        throw new ApplicationException("You cannot delete me!");
    }
}
