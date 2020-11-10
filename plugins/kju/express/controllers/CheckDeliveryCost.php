<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\District;
use Kju\Express\Models\Service;
use October\Rain\Exception\ValidationException;

class CheckDeliveryCost extends Controller
{
    public $implement = [];

    public $requiredPermissions = [
        'access_check_delivery_cost'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'deliveries', 'check-delivery-cost');
    }


    public function index()
    {

        $services = DB::table('kju_express_services AS service')
            ->select(DB::raw("CONCAT(service.code,', ',service.name) AS name"), 'service.code')
            ->get()->pluck('name', 'code');

        $this->vars['services'] = $services;
    }

    public function onGetBranches()
    {
        $q = input('q');
        return [
            'result' => DB::table('kju_express_districts AS district')
            ->join('kju_express_regencies AS regency', 'district.regency_id', '=', 'regency.id')
            ->join('kju_express_provinces AS province', 'regency.province_id', '=', 'province.id')
            ->select(DB::raw("CONCAT(district.name,', ',regency.name,', ',province.name) AS name"), 'district.id')
            ->take(20)
            ->where('district.name', 'like', "%$q%")
            ->orWhere('regency.name', 'like', "%$q%")
            ->get()->pluck('name', 'id')
        ];
    }

    public function onGetDestinations()
    {
        $q = input('q');
        return [
            'result' => DB::table('kju_express_districts AS district')
                ->join('kju_express_regencies AS regency', 'district.regency_id', '=', 'regency.id')
                ->join('kju_express_provinces AS province', 'regency.province_id', '=', 'province.id')
                ->select(DB::raw("CONCAT(district.name,', ',regency.name,', ',province.name) AS name"), 'district.id')
                ->take(20)
                ->where('district.name', 'like', "%$q%")
                ->orWhere('regency.name', 'like', "%$q%")
                ->get()->pluck('name', 'id')
        ];
    }

    public function onLoadWeight(){
        $service = Service::find(input('service_code'));

        $this->vars['service'] = $service;
    }

    public function onCheckDeliveryCost(){
        
        $src_district_id = input('src_district_id');
        $dst_district_id = input('dst_district_id');

        $service_code = input('service_code');

        $weight = input('weight');

        $validator = Validator::make(
            [
                'src_district_id' => $src_district_id,
                'dst_district_id' => $dst_district_id,
                'service_code' => $service_code,
            ],
            [
                'src_district_id' => 'required',
                'dst_district_id' => 'required',
                'service_code' => 'required',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $service = Service::find($service_code);
        
        $validator = Validator::make(
            [
                'weight' => $weight,
                'weight_limit' => $service->weight_limit,
            ],
            [
                'weight' => 'required_unless:weight_limit,-1',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }


        $cost = DB::table('kju_express_delivery_costs AS cost')
            ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_id', '=', 'route.id')
             ->select('cost.cost','cost.add_cost')
            ->where('cost.service_code', $service_code)
            ->where('route.src_district_id', $src_district_id)
            ->where('route.dst_district_id', $dst_district_id)
            ->first();


        if(isset($cost)){
            if($service->weight_limit == -1){
                $this->vars['total_cost'] = $cost->cost;
            }else {
                $add_cost = ($weight - $service->weight_limit) * $cost->add_cost;
                $add_cost = $add_cost < 0 ? 0 : $add_cost;
                $this->vars['total_cost'] = $add_cost + $cost->cost;
            }
        }else {
            $this->vars['total_cost'] = null;
        }

    }

}
