<?php namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Provinces extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'access_provinces' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'region_data', 'provincies');
    }
}
