<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\District;
use Kju\Express\Models\Region;
use Kju\Express\Models\Service;
use October\Rain\Exception\ValidationException;

class CheckDeliveryCost extends Controller
{
    public $implement = [];

    public $pageTitle = "kju.express::lang.delivery_cost.check";

    public $requiredPermissions = [
        'access_check_delivery_cost'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'delivery-data', 'check-delivery-cost');
    }


    public function index()
    {

        $services = DB::table('kju_express_services AS service')
            ->select(DB::raw("CONCAT(service.code,', ',service.name) AS name"), 'service.code')
            ->get()->pluck('name', 'code');

        $this->vars['services'] = $services;
    }

    public function onGetSources()
    {
        $q = input('q');
        return [
            'result' => Region::where('name', 'like', "%$q%")
                ->where('type', "regency")
                ->get()->take(20)->pluck('name', 'id')
        ];
    }

    public function onGetDestinations()
    {
        $q = input('q');
        $select = DB::select("
            SELECT district.id AS id,IF(district.type = 'regency',district.name,CONCAT(district.name,', ',regency.name)) AS display_name 
            FROM kju_express_regions district
            LEFT JOIN kju_express_regions regency ON district.parent_id = regency.id
            WHERE (district.type = 'district' OR district.type = 'regency')
            AND district.name LIKE '%$q%' OR (district.type ='district' AND regency.name LIKE '%$q%')
            LIMIT 20
        ");
        return [
            'result' => array_column($select,'display_name','id')
        ];
    }

    public function onLoadWeight()
    {
        $service = Service::find(input('service_code'));
        $this->vars['service'] = $service;
    }

    public function onCheckDeliveryCost()
    {

        $src_region_id = input('src_region_id');
        $dst_region_id = input('dst_region_id');

        $service_code = input('service_code');

        $weight = input('weight');

        $validator = Validator::make(
            [
                'src_region_id' => $src_region_id,
                'dst_region_id' => $dst_region_id,
                'service_code' => $service_code,
            ],
            [
                'src_region_id' => 'required',
                'dst_region_id' => 'required',
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

        $dst_regency_id = substr($dst_region_id,0,4);
        $cost = DB::table('kju_express_delivery_costs AS cost')
            ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_code', '=', 'route.code')
            ->select('cost.cost', 'cost.add_cost')
            ->where('cost.service_code', $service_code)
            ->where('route.src_region_id', $src_region_id)
            ->whereIn('route.dst_region_id', [$dst_region_id,$dst_regency_id])
            ->orderByRaw("FIELD(route.dst_region_id,'$dst_region_id','$dst_regency_id')")
            ->first();


        $this->vars['service'] = $service;
        $this->vars['src_region'] = Region::find($src_region_id);
        $this->vars['dst_region'] = Region::find($dst_region_id);
        $this->vars['weight'] = $weight;

        if (isset($cost)) {
            if ($service->weight_limit == -1) {
                $this->vars['total_cost'] = $cost->cost;
            } else {
                $add_cost = ($weight - $service->weight_limit) * $cost->add_cost;
                $add_cost = $add_cost < 0 ? 0 : $add_cost;
                $this->vars['total_cost'] = $add_cost + $cost->cost;
            }
        } else {
            $this->vars['total_cost'] = null;
           
        }

    }
}
