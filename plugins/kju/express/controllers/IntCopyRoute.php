<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use Kju\Express\Models\IntAddDeliveryCost;
use Kju\Express\Models\IntDeliveryCost;
use Kju\Express\Models\IntDeliveryRoute;
use October\Rain\Support\Facades\Flash;

class IntCopyRoute extends Controller
{

    public $requiredPermissions = [
        'is_superuser'
    ];

    public function index()
    {
    }

    public function onCopy()
    {
        $origin = post('origin');
        $copy_origins = explode(';', post('copy_origins'));
        $origin_routes =  IntDeliveryRoute::where("src_region_id", $origin)->get();

        foreach ($copy_origins as $copy_origin) {
            $copy_origin_route_ids = IntDeliveryRoute::where("src_region_id", $copy_origin)
                ->pluck('id');

            IntDeliveryCost::whereIn('int_delivery_route_id', $copy_origin_route_ids)
                ->delete();

            IntAddDeliveryCost::whereIn('int_delivery_route_id', $copy_origin_route_ids)
                ->delete();

            IntDeliveryRoute::where("src_region_id", $copy_origin)
                ->delete();
        }

        foreach ($origin_routes as $origin_route) {
            $origin_costs = IntDeliveryCost::where('int_delivery_route_id', $origin_route->id)
                ->get();

            $origin_add_costs = IntAddDeliveryCost::where('int_delivery_route_id', $origin_route->id)
                ->get();

            foreach ($copy_origins as $copy_origin) {

                $copy_origin_route = new IntDeliveryRoute();
                $copy_origin_route->src_region_id = $copy_origin;
                $copy_origin_route->dst_region_id = $origin_route->dst_region_id;
                $copy_origin_route->code =
                    "$copy_origin_route->src_region_id-$copy_origin_route->dst_region_id";

                $copy_origin_route->save();

                foreach ($origin_costs as $origin_cost) {

                    $copy_origin_cost = new IntDeliveryCost();
                    $copy_origin_cost->min_range_weight = $origin_cost->min_range_weight;
                    $copy_origin_cost->max_range_weight = $origin_cost->max_range_weight;
                    $copy_origin_cost->base_cost_per_kg =  $origin_cost->base_cost_per_kg;
                    $copy_origin_cost->profit_percentage =  $origin_cost->profit_percentage;

                    $copy_origin_cost->route()
                        ->associate($copy_origin_route);

                    $copy_origin_cost->save();
                }

                foreach ($origin_add_costs as $origin_add_cost) {

                    $copy_origin_add_cost = new IntAddDeliveryCost();
                    $copy_origin_add_cost->add_cost_per_kg = $origin_add_cost->add_cost_per_kg;
                    $copy_origin_add_cost->goods_type_id = $origin_add_cost->goods_type_id;
     

                    $copy_origin_add_cost->route()
                        ->associate($copy_origin_route);

                    $copy_origin_add_cost->save();
                }
            }
        }

        Flash::info('OK BREE');
    }
}
