<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ManifestDeliveryOrders extends Controller
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

}
