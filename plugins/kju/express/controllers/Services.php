<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Services extends Controller
{
    public $implement = ['Backend\Behaviors\ListController',        'Backend\Behaviors\FormController', 'Backend\Behaviors\ReorderController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = [
        'access_services'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'master-data', 'services');
    }
}
