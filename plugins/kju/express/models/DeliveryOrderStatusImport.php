<?php

namespace Kju\Express\Models;



/**
 * Model
 */
class DeliveryOrderStatusImport extends \Backend\Models\ImportModel
{


    public $rules = [];

    public $belongsTo = [
        'region' => ['Kju\Express\Models\Region', 'key' => 'region_id'],
    ];

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {

            try {

                $order_code = $data['delivery_order_code'];
                $status = $data['status'];
                $region_id = $data['region_id'];

                $delivery_order = DeliveryOrder::where('code',$order_code)
                    ->first();

                $data['delivery_order_id'] = $delivery_order->id;

                $statuses = ['transit', 'received', 'failed'];

                if (!in_array($status, $statuses)) {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.status_not_allowed'))
                    );
                    continue;
                }

                if (empty($order_code)) {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.order_code_not_found'))
                    );
                    continue;
                }

                if (empty($delivery_order)) {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.order_code_not_found'))
                    );
                    continue;
                }

                // if ($delivery_order->status == 'pickup') {
                //     $this->logError(
                //         $row,
                //         e(trans('kju.express::lang.global.delivery_order_still_pickup'))
                //     );
                //     continue;
                // }

                if ($delivery_order->status == 'received' || $delivery_order->status == 'failed') {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.delivery_order_already_finish'))
                    );
                    continue;
                }


                $new_order_status = new DeliveryOrderStatus();

                $new_order_status->fill($data);

                if ($status == 'received') {
                    $new_order_status->region = $delivery_order->consignee_region->parent;
                } else {
                    $region = Region::find($region_id);
                    if (empty($region)) {
                        $this->logError(
                            $row,
                            e(trans('kju.express::lang.global.region_id_not_found'))
                        );
                        continue;
                    }

                    if ($region->type != 'regency') {
                        $this->logError(
                            $row,
                            e(trans('kju.express::lang.global.region_id_not_found'))
                        );
                        continue;
                    }
                    $new_order_status->region = $region;
                }

                $new_order_status->save();

                $delivery_order->status = $new_order_status->status;
                $delivery_order->save();


                $this->logCreated();
            } catch (\Exception $ex) {
                trace_log($ex);
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
