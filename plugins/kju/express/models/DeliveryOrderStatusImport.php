<?php namespace Kju\Express\Models;



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
        'region' => ['Kju\Express\Models\Region','key' => 'region_id'],
    ];

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {

            try {
                $subscriber = new Subscriber;
                $subscriber->fill($data);
                $subscriber->save();

                $this->logCreated();
            }
            catch (\Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }

        }
    }
}
