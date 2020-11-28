<?php namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class DeliveryOrders extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';
    
    public $bodyClass = 'compact-container';

    public $requiredPermissions = [
        'access_delivery_orders' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'delivery-orders');
    }
}
