<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class DeliveryOrderStatuses extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\ImportExportController',
    ];

    public $listConfig = 'config_list.yaml';

    public $importExportConfig = 'config_import_export.yaml';

    public $requiredPermissions = [
        'access_delivery_order_statuses'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'import-status', 'delivery-order-statuses');
    }
}
