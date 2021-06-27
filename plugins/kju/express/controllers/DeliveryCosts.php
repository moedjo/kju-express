<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\DB;

class DeliveryCosts extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\ImportExportController'
    ];

    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public $requiredPermissions = [
        'access_delivery_routes'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'master-route-data', 'delivery-costs');
    }
}
