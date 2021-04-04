<?php

namespace Kju\Express\Models;

use Illuminate\Support\Facades\Log;

/**
 * Model
 */
class IntDeliveryOrderStatusImport extends \Backend\Models\ImportModel
{


    public $rules = [
        'order_code' => 'required',
        'tracking_number' => 'required'
    ];

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {

            try {

                $order_code = $data['order_code'];
                $tracking_number = $data['tracking_number'];
                $description = $data['description'];

                $delivery_order = IntDeliveryOrder::where('code',$order_code)->first();


                if (empty($delivery_order)) {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.order_code_not_found'))
                    );
                    continue;
                }

                $statuses = ['process', 'export'];

                if (!in_array($delivery_order->status, $statuses)) {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.status_not_allowed'))
                    );
                    continue;
                }


                
                $delivery_order->tracking_number = $tracking_number;
                $delivery_order->status = 'export';
                $delivery_order->save();

                $new_order_status = new IntDeliveryOrderStatus();
                $new_order_status->int_delivery_order_id = $delivery_order->id;
                $new_order_status->description = $description;
                $new_order_status->status = 'export';
                $new_order_status->save();

                $this->logCreated();
            } catch (\Exception $ex) {

                Log::alert($ex);

                $this->logError($row, $ex->getMessage());
            }
        }
    }

    public function getStatusOptions()
    {
        return [
            'transit' => 'kju.express::lang.global.transit',
            'received' => 'kju.express::lang.global.received',
            'failed' => 'kju.express::lang.global.failed',
        ];
    }
}
