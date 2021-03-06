<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class IntDeliveryOrderStatuses extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\ImportExportController',
    ];

    public $listConfig = 'config_list.yaml';

    public $importExportConfig = 'config_import_export.yaml';

    public $requiredPermissions = [
        'access_int_delivery_order_statuses'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'import-status', 'int-delivery-order-statuses');
    }
}
