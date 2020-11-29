<?php

namespace Kju\Express\Models;



/**
 * Model
 */
class DeliveryOrderStatusImport extends \Backend\Models\ImportModel
{
    /**
     * @var array The rules to be applied to the data.
     */
    public $rules = [];

    public $belongsTo = [
        'region' => ['Kju\Express\Models\Region', 'key' => 'region_id'],
    ];

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {

            try {
                $order_status = new DeliveryOrderStatus();

                $order_status->fill($data);
                $order_status->region = $this->region;
                $order_status->save();
                
                $this->logCreated();
            } catch (\Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }
}
