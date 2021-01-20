<?php namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class IntDeliveryOrders extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'access_int_delivery_orders' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'international', 'int-delivery-orders');
    }
}
