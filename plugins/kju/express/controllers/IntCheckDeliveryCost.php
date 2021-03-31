<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\DeliveryCost;
use Kju\Express\Models\District;
use Kju\Express\Models\GoodsType;
use Kju\Express\Models\IntAddDeliveryCost;
use Kju\Express\Models\IntDeliveryCost;
use Kju\Express\Models\IntDeliveryRoute;
use Kju\Express\Models\Region;
use Kju\Express\Models\Service;
use October\Rain\Exception\ValidationException;

class IntCheckDeliveryCost extends Controller
{
    public $implement = [];

    public $pageTitle = "kju.express::lang.int_delivery_cost.check";

    public $requiredPermissions = [
        'access_int_delivery_orders'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'international', 'int-check-delivery-cost');
    }


    public function index()
    {
        $this->vars['goods_types'] = GoodsType::all()->pluck('name','id');
    }

    public function onGetSources()
    {
        $q = input('q');
        return [
            'result' => Region::where('name', 'like', "%$q%")
                ->where('type', "province")
                ->get()->take(20)->pluck('name', 'id')
        ];
    }

    public function onGetDestinations()
    {
        $q = input('q');
        $result = Region::where('name', 'like', "%$q%")
            ->where('type', "country")
            ->get()->take(20)->pluck('name', 'id');
        return [
            'result' => $result
        ];
    }

    public function onCheckDeliveryCost()
    {

        $src_region_id = input('src_region_id');
        $dst_region_id = input('dst_region_id');
        $goods_type_id = input('goods_type_id');
        $weight = ceil(input('weight'));

        $validator = Validator::make(
            [
                'src_region_id' => $src_region_id,
                'dst_region_id' => $dst_region_id,
                'goods_type_id' => $goods_type_id,
                'weight' => $weight,
            ],
            [
                'src_region_id' => 'required',
                'dst_region_id' => 'required',
                'goods_type_id' => 'required',
                'weight' => 'required|numeric|min:1',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $goods_type = GoodsType::findOrFail($goods_type_id);

        $route_code = $src_region_id.'-'.$dst_region_id;
        $route = IntDeliveryRoute::where('code',$route_code)->first();
        
        $int_delivery_cost = IntDeliveryCost::where('int_delivery_route_id', $route->id)
            ->whereRaw("$weight BETWEEN min_range_weight AND max_range_weight")
            ->first();
 
        $this->vars['goods_type'] = $goods_type;
        $this->vars['src_region'] = Region::find($src_region_id);
        $this->vars['dst_region'] = Region::find($dst_region_id);
        $this->vars['weight'] = $weight;

        if (isset($int_delivery_cost)) {
            $this->vars['cost'] = $int_delivery_cost;
            $total_cost = $int_delivery_cost->base_cost_per_kg * $weight;
            $total_cost = $total_cost +
                ($total_cost * ($int_delivery_cost->profit_percentage/100)) ;
            
            $route = IntDeliveryRoute::where('code',$route_code)->first();
            $int_add_delivery_cost = IntAddDeliveryCost::where('int_delivery_route_id', $route->id)
                ->where('goods_type_id',$goods_type_id)
                ->first();

            if(isset($int_add_delivery_cost)){
                $add_cost = $int_add_delivery_cost->add_cost_per_kg * $weight;
                $total_cost = $total_cost + $add_cost;
            }

            $this->vars['total_cost'] = $total_cost;
        } else {
            $this->vars['total_cost'] = null;
        }

    }
}
