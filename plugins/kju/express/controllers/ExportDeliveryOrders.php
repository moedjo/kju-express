<?php namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ExportDeliveryOrders extends Controller
{
    public $implement = [    ];
    
    public $requiredPermissions = [
        'access_pickups' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'report', 'export-delivery-orders');
    }
}
