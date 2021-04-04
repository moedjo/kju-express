<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use Kju\Express\Models\DeliveryCost;
use Kju\Express\Models\DeliveryRoute;
use October\Rain\Support\Facades\Flash;

class CopyRoute extends Controller
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
        $origin_routes =  DeliveryRoute::where("src_region_id", $origin)->get();

        foreach ($copy_origins as $copy_origin) {
            $copy_origin_route_ids = DeliveryRoute::where("src_region_id", $copy_origin)
                ->pluck('id');

            DeliveryCost::whereIn('delivery_route_id', $copy_origin_route_ids)
                ->delete();

            DeliveryRoute::where("src_region_id", $copy_origin)
                ->delete();
        }

        foreach ($origin_routes as $origin_route) {
            $origin_costs = DeliveryCost::where('delivery_route_id', $origin_route->id)->get();

            foreach ($copy_origins as $copy_origin) {

                $copy_origin_route = new DeliveryRoute();
                $copy_origin_route->src_region_id = $copy_origin;
                $copy_origin_route->dst_region_id = $origin_route->dst_region_id;
                $copy_origin_route->code =
                    "$copy_origin_route->src_region_id-$copy_origin_route->dst_region_id";

                $copy_origin_route->save();

                foreach ($origin_costs as $origin_cost) {

                    $copy_origin_cost = new DeliveryCost();
                    $copy_origin_cost->cost = $origin_cost->cost;
                    $copy_origin_cost->add_cost = $origin_cost->add_cost;
                    $copy_origin_cost->min_lead_time =  $origin_cost->min_lead_time;
                    $copy_origin_cost->max_lead_time =  $origin_cost->max_lead_time;
                    $copy_origin_cost->service_id =  $origin_cost->service_id;

                    $copy_origin_cost->route()
                        ->associate($copy_origin_route);

                    $copy_origin_cost->save();
                }
            }
        }

        Flash::info('OK');
    }
}
