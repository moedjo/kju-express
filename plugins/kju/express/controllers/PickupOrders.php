<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Kju\Express\Models\DeliveryOrder;
use Renatio\DynamicPDF\Classes\PDF;

class PickupOrders extends Controller
{
    public $implement = [
        'Backend\Behaviors\ImportExportController',
    ];

    public $importExportConfig = 'config_import_export.yaml';


    public $requiredPermissions = [
        'access_delivery_orders'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'delivery-orders');
    }

    // public function export(){
    //     return $this->asExtension('ImportExportController')->export();
    // }
}
