<?php

namespace Kju\Express\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kju\Express\Models\DeliveryCost;
use Kju\Express\Models\DeliveryOrder;
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
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $delivery_order = DeliveryOrder::with(['statuses'])->find($delivery_order_code);
        $this->page['delivery_order'] = $delivery_order;
    }


    public function onCheckCost()
    {
        $source = input('source');
        $destination = input('destination');
        $weight = input('weight');
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

        $this->page['source'] = Region::findOrFail($source);
        $this->page['destination'] = Region::findOrFail($destination);

        // District Level
        $costs = DeliveryCost::whereHas('route', function ($query) {
            $query->where('src_region_id', input('source'))
                ->where('dst_region_id', input('destination'));
        })->get()
            ->sortBy(function ($cost, $key) {
                return $cost->service->sort_order;
            });

        $this->page['costs'] = null;
        if (isset($costs)) {
            $this->page['costs'] = $costs;
        } else {
            // Regency Level
            $costs = DeliveryCost::whereHas('route', function ($query) {
                $regency = substr(input('destination'), 0, 4);
                $query->where('src_region_id', input('source'))
                    ->where('dst_region_id', $regency);
            })->get()->sortBy(function ($cost, $key) {
                return $cost->service->sort_order;
            });;

            if (isset($costs)) {
                $this->page['costs'] = $costs;
            }
        }
    }


    public function onRender()
    {
    }
}
