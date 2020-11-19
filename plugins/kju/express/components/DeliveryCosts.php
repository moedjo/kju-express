<?php

namespace Kju\Express\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\DeliveryCost;
use October\Rain\Exception\ValidationException;
use Kju\Express\Models\Region;

class DeliveryCosts extends \Cms\Classes\ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Delivery Costs',
            'description' => 'Delivery Costs Component'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
    }

    public function onGetSource()
    {
        $q = input('q');
        return [
            'results' => Region::where('name', 'like', "%$q%")
                ->select('id','name AS text')
                ->where('type', "regency")
                ->take(20)
                ->get()
        ];
    }

    public function onGetDestination(){
        $q = input('q');
        $select = DB::select("
            SELECT district.id AS id,IF(district.type = 'regency',district.name,CONCAT(district.name,', ',regency.name)) AS text 
            FROM kju_express_regions district
            LEFT JOIN kju_express_regions regency ON district.parent_id = regency.id
            WHERE (district.type = 'district' OR district.type = 'regency')
            AND district.name LIKE '%$q%' OR (district.type ='district' AND regency.name LIKE '%$q%')
            LIMIT 20
        ");
        return [
            'results' => $select
        ];
    }


    public function onCheckCost(){
        $source = input('source');
        $destination = input('destination');
        $weight = input('weight');
        $this->page['weight'] = $weight;


        $validator = Validator::make(
            [
                'source' => $source,
                'destination' => $destination,
                'weight' => $weight,
            ],
            [
                'source' => 'required',
                'destination' => 'required',
                'weight' => 'required',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->page['source'] = Region::findOrFail($source);
        $this->page['destination'] = Region::findOrFail($destination);

        // District Level
        $costs = DeliveryCost::whereHas('delivery_route',function($query){
            $query->where('src_region_id',input('source'))
                ->where('dst_region_id', input('destination'));
        })->get();

        $this->page['costs'] = null;
        if (isset($costs)) {
            $this->page['costs'] = $costs;
        } else {
             // Regency Level
            $regency = substr($destination,0,4);
            $costs = DB::table('kju_express_delivery_costs AS cost')
            ->join('kju_express_delivery_routes AS route', 'cost.delivery_route_code', '=', 'route.code')
            ->where('route.src_region_id', $source)
            ->where('route.dst_region_id', $regency)
            ->get();

            if (isset($costs)) {
                $this->page['costs'] = $costs;
            }
        }

    }


    public function onRender()
    {
    }
}
