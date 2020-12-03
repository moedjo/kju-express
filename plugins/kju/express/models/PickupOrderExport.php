<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PickupOrderExport extends \Backend\Models\ExportModel
{

    protected $fillable = [
        'pickup_date'
    ];

    public function exportData($columns, $sessionKey = null)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;

        $query = DeliveryOrder::with([
            'branch_region',
            'customer',
            'pickup_region',
            'pickup_courier'
        ])->whereDate('pickup_date', $this->pickup_date);

        if (!$user->isSuperUser()) {
            if (isset($branch)) {
                $query = $query->where('branch_code', $branch->code);
            } else {
                $query = $query->where('branch_code', -1);
            }
        }

        $query->where('status','pickup');
        $query->where('pickup_request',true);
        $query->orderBy('pickup_courier_user_id');
        $query->orderBy('pickup_region_id');

        $delivery_orders = $query->get();

        $delivery_orders->each(function ($delivery_order) use ($columns) {
            $delivery_order->addVisible($columns);
            $delivery_order->addVisible('branch_region');
            $delivery_order->addVisible('customer');
            $delivery_order->addVisible('pickup_region');
            $delivery_order->addVisible('pickup_courier');

            $delivery_order->code = '#'.$delivery_order->code;

            $delivery_order->customer->phone_number = '#'.$delivery_order->customer->phone_number;
            $delivery_order->display_pickup_date =  $delivery_order->pickup_date->format('d, M Y');

            $delivery_order->consignee_region->displayName = $delivery_order->consignee_region->displayName;
        });
        return $delivery_orders->toArray();
    }
}
