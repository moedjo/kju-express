<?php

namespace Kju\Express\Models;

use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\DB;

class ManifestDeliveryOrderExport extends \Backend\Models\ExportModel
{

    protected $fillable = [
        'process_date','type'
    ];

    public function exportData($columns, $sessionKey = null)
    {

        $user = BackendAuth::getUser();
        $branch = $user->branch;
        $branch_id = isset($branch) ? $branch->id : null;
        $branch_region_id = isset($branch) ? $branch->region->id : null;

        $query = DeliveryOrder::with([
            'branch_region',
            'consignee_region',
            'customer'
        ])->whereDate('process_at', $this->process_date);


        if ($user->isSuperUser()) {
        } else if ($this->type == 'outgoing') {
            $query = $query->where('branch_id',  $branch_id);
        } else if ($this->type == 'incoming') {
            $query = $query->where(DB::raw('SUBSTR(consignee_region_id,1,4)'),  $branch_region_id);
        } else {
            $query = $query->where('code',  -1);
        }


        $query->orderBy('consignee_region_id');
        $query->orderBy('process_at');

        $delivery_orders = $query->get();

        $delivery_orders->each(function ($delivery_order) use ($columns) {
            $delivery_order->addVisible($columns);
            $delivery_order->addVisible('branch_region');
            $delivery_order->addVisible('consignee_region');
            $delivery_order->addVisible('customer');
            $delivery_order->addVisible('service');


            $delivery_order->consignee_region->displayName = $delivery_order->consignee_region->displayName;
            
        });

        return $delivery_orders->toArray();
    }
}
