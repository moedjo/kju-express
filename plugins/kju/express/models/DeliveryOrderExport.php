<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryOrderExport extends \Backend\Models\ExportModel
{

    protected $fillable = [
        'process_date'
    ];

    public function exportData($columns, $sessionKey = null)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;

        $query = DeliveryOrder::with([
            'branch_region',
            'consignee_region',
            'customer'
        ])->whereDate('process_at', $this->process_date);

        if (!$user->isSuperUser()) {
            if (isset($branch)) {
                $query = $query->where('branch_code', $branch->code);
            } else {
                $query = $query->where('branch_code', -1);
            }
        }


        $query->orderBy('consignee_region_id');
        $query->orderBy('process_at');

        $delivery_orders = $query->get();

        $delivery_orders->each(function ($delivery_order) use ($columns) {
            $delivery_order->addVisible($columns);
            $delivery_order->addVisible('branch_region');
            $delivery_order->addVisible('consignee_region');
            $delivery_order->addVisible('customer');

            // $delivery_order->code = '#'.$delivery_order->code;

            $delivery_order->consignee_region->displayName = $delivery_order->consignee_region->displayName;
        });
        return $delivery_orders->toArray();
    }
}
