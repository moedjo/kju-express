<?php namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class IntDeliveryRoutes extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'
    ];
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';


    public $requiredPermissions = [
        'access_int_delivery_routes' 
    ]; 

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'master-data', 'int-delivery-routes');
    }
}
