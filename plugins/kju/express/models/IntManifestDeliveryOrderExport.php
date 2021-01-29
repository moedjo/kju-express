<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\DB;

class IntManifestDeliveryOrderExport extends \Backend\Models\ExportModel
{

    protected $fillable = [
        'process_date','type'
    ];

    public function exportData($columns, $sessionKey = null)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;
        $branch_code = isset($branch) ? $branch->code : null;
        $branch_region_id = isset($branch) ? $branch->region->id : null;

        $query = IntDeliveryOrder::with([
            'goods_type',
            'origin_region',
            'consignee_region',
            'customer'
        ])->whereDate('process_at', $this->process_date);


        if ($user->isSuperUser()) {
            
        } else if ($this->type == 'outgoing') {
            $query = $query->where('branch_code',  $branch_code);
        } else {
            $query = $query->where('code',  -1);
        }


        $query->orderBy('consignee_region_id');
        $query->orderBy('process_at');

        $delivery_orders = $query->get();

        $delivery_orders->each(function ($delivery_order) use ($columns) {
            $delivery_order->addVisible($columns);
            $delivery_order->addVisible('origin_region');
            $delivery_order->addVisible('goods_type');
            $delivery_order->addVisible('consignee_region');
            $delivery_order->addVisible('customer');

            $delivery_order->consignee_region->displayName = $delivery_order->consignee_region->displayName;
        });
        return $delivery_orders->toArray();
    }
}
