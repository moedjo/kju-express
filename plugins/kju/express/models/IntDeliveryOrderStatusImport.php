<?php

namespace Kju\Express\Models;



/**
 * Model
 */
class IntDeliveryOrderStatusImport extends \Backend\Models\ImportModel
{


    public $rules = [];

    public $belongsTo = [
        'region' => ['Kju\Express\Models\Region', 'key' => 'region_id'],
    ];

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {

            try {

                $order_code = $data['int_delivery_order_code'];
                $status = $data['status'];
                $region_id = $data['region_id'];

                $delivery_order = IntDeliveryOrder::find($order_code);



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


                if ($delivery_order->status == 'received' || $delivery_order->status == 'failed') {
                    $this->logError(
                        $row,
                        e(trans('kju.express::lang.global.delivery_order_already_finish'))
                    );
                    continue;
                }


                $new_order_status = new IntDeliveryOrderStatus();

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

                    if ($region->type != 'country') {
                        $this->logError(
                            $row,
                            e(trans('kju.express::lang.global.region_id_not_found'))
                        );
                        continue;
                    }
                    $new_order_status->region = $region;
                }

                $new_order_status->save();


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
