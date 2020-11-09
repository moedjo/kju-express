<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\District;
use Kju\Express\Models\Service;

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
            'result' => DB::table('kju_express_branches AS branch')
                ->join('kju_express_districts AS district', 'branch.district_id', '=', 'district.id')
                ->join('kju_express_regencies AS regency', 'district.regency_id', '=', 'regency.id')
                ->join('kju_express_provinces AS province', 'regency.province_id', '=', 'province.id')
                ->select(DB::raw("CONCAT(branch.code,' - ',branch.name,', ',district.name,', ',regency.name,', ',province.name) AS name"), 'branch.code AS code')
                ->take(20)
                ->where('district.name', 'like', "%$q%")
                ->orWhere('regency.name', 'like', "%$q%")
                ->orWhere('branch.name', 'like', "%$q%")
                ->get()->pluck('name', 'code')
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
        
        $branch_code = input('branch_code');
        $district_id = input('district_id');

        $service_code = input('service_code');

        $service = Service::find($service_code);

        $weight = input('weight');

        $validator = Validator::make(
            [
                'branch_code' => $branch_code,
                'district_id' => $district_id,
                'service_code' => $service_code,
                'weight' => $weight,
                'weight_limit' => $service->weight_limit,
            ],
            [
                'branch_code' => 'required',
                'district_id' => 'required',
                'service_code' => 'required',
                'weight' => 'required_unless:weight_limit,-1',
            ]
        );
    }
}
