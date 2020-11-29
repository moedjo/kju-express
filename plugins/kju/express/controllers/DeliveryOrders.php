<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class DeliveryOrders extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'
    ];

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


    public function formExtendModel($model)
    {
        $context = $this->formGetContext();
        $user = $this->user;
        $branch = $user->branch;
        if ($context == 'create') {
            if (!$user->isSuperUser()) {
                if (isset($branch)) {
                    $model->branch = $branch;
                    $model->branch_region = $branch->region;
                }
            }
        }
    }

    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->trashed()) {
            return 'strike';
        }
    }

    public function listExtendQuery($query)
    {
        // $query->withTrashed();
    }

}
