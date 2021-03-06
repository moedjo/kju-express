<?php

namespace Kju\Express\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\DeliveryCost;
use Kju\Express\Models\DeliveryOrder;
use Kju\Express\Models\GoodsType;
use Kju\Express\Models\IntAddDeliveryCost;
use Kju\Express\Models\IntDeliveryCost;
use Kju\Express\Models\IntDeliveryOrder;
use October\Rain\Exception\ValidationException;
use Kju\Express\Models\Region;
use Multiwebinc\Recaptcha\Validators\RecaptchaValidator;
use October\Rain\Support\Facades\Flash;

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
                ->select('id', 'name AS text')
                ->where('type', "regency")
                ->take(20)
                ->get()
        ];
    }

    public function onGetDestination()
    {
        $q = input('q');
        $select = DB::select("
            SELECT 
                district.id AS id,
                IF(district.type = 'regency' OR district.type = 'country',
                    district.name,
                    CONCAT(district.name,', ',regency.name)) 
                AS text 
            FROM kju_express_regions district
            LEFT JOIN kju_express_regions regency ON district.parent_id = regency.id
            WHERE 
                (
                    district.type = 'district' OR 
                    district.type = 'regency' OR 
                    district.type = 'country'
                )
            AND district.name LIKE '%$q%' OR (district.type ='district' AND regency.name LIKE '%$q%')
            LIMIT 20
        ");
        return [
            'results' => $select
        ];
    }


    public function onCheckStatus()
    {
        $delivery_order_code = input('code');
        // TODO recaptha
        // TODO sorting status with timestamp
        // $validator = Validator::make(
        //     [
        //         'g-recaptcha-response' => input('g-recaptcha-response'),
        //     ],
        //     [
        //         'g-recaptcha-response' => [
        //             'required',
        //             new RecaptchaValidator,
        //         ],
        //     ]
        // );
        // if ($validator->fails()) {
        //     throw new ValidationException($validator);
        // }
        $delivery_order = DeliveryOrder::with(['statuses'])->find($delivery_order_code);
        

        // for international
        if(empty($delivery_order)){
            $delivery_order = IntDeliveryOrder::with(['statuses'])->find($delivery_order_code);
       
        }

        $this->page['delivery_order'] = $delivery_order;

    }


    public function onCheckCost()
    {
        $source = input('source');
        $destination = input('destination');
        $weight = ceil(input('weight'));
        $this->page['weight'] = $weight;


        $validator = Validator::make(
            [

                'g-recaptcha-response' => input('g-recaptcha-response'),
                'source' => $source,
                'destination' => $destination,
                'weight' => $weight,
            ],
            [
                'g-recaptcha-response' => [
                    'required',
                    new RecaptchaValidator,
                ],
                'source' => 'required',
                'destination' => 'required',
                'weight' => 'required|numeric|between:0,100000',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $source = Region::findOrFail($source);
        $destination = Region::findOrFail($destination);

        $this->page['source'] = $source;
        $this->page['destination'] = $destination;
        $this->page['flag'] = 'dom';

        // International Level
        if ($destination->type == 'country') {
            $this->page['flag'] = 'int';
            $route_code = $source->parent->id . '-' . $destination->id;
            $costs = array();
            $int_delivery_cost = IntDeliveryCost::where('int_delivery_route_code', $route_code)
                ->whereRaw("$weight BETWEEN min_range_weight AND max_range_weight")
                ->first();

            if(empty($int_delivery_cost)){
                return;
            }

            $total_cost = $int_delivery_cost->base_cost_per_kg * $weight;
            $total_cost = $total_cost +
                ($total_cost * ($int_delivery_cost->profit_percentage / 100));

            $goods_types = GoodsType::all();

            $int_add_delivery_costs = IntAddDeliveryCost::where('int_delivery_route_code', $route_code)
                ->get()->pluck('add_cost_per_kg','goods_type_code');
                trace_log($int_add_delivery_costs);
            foreach ($goods_types as $goods_type) {

                $add_cost_per_kg = isset($int_add_delivery_costs[$goods_type->code]) ? 
                    $int_add_delivery_costs[$goods_type->code] 
                    : 0;

                $add_cost = $add_cost_per_kg * $weight;
                $costs[] = [
                    'goods_type_name' => $goods_type->name,
                    'total_cost' => $total_cost + $add_cost,
                ];
            }


            $this->page['costs'] = $costs;
            return;
        }




        // Domestic Level
        $costs = DeliveryCost::whereHas('route', function ($query) {
            $query
                ->where('src_region_id', input('source'))
                ->where(function ($query) {
                    $district = input('destination');
                    $regency = substr($district, 0, 4);
                    $query
                        ->where('dst_region_id', $district)
                        ->orWhere('dst_region_id', $regency);
                });
        })
            ->with(['service' => function ($query) {
            }])
            ->orderBy('delivery_route_code', 'desc')
            ->get()
            ->unique('service_code')
            ->sortBy(function ($cost, $key) {
                return $cost->service->sort_order;
            });

        $this->page['costs'] = $costs;
    }


    public function onRender()
    {
    }
}
