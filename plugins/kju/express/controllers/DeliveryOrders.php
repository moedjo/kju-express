<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Kju\Express\Models\DeliveryOrder;
use Renatio\DynamicPDF\Classes\PDF;

class DeliveryOrders extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
        'Backend\Behaviors\ImportExportController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public $bodyClass = 'compact-container';

    public $requiredPermissions = [
        'access_delivery_orders'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'delivery-orders');
    }





    public function export(){
        $this->bodyClass = '';

        return $this->asExtension('ImportExportController')->export();
    }


    public function print($code)
    {

        $delivery_order = DeliveryOrder::findOrFail($code);

        $result = ['delivery_order' => $delivery_order];

        if ($delivery_order->weight_limit == -1) {
            return PDF::loadTemplate('delivery_order_no_weight', $result)->stream();
        } else {
            return PDF::loadTemplate('delivery_order', $result)->stream();
        }
    }


    public function formExtendModel($model)
    {
        $context = $this->formGetContext();
        $user = $this->user;
        $branch = $user->branch;
        if ($context == 'create') {
            if ($user->isSuperUser()) {
            } else if (isset($branch)) {
                $model->branch = $branch;
                $model->branch_region = $branch->region;
            } else {
            }
        }
    }

    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->trashed()) {
            return 'strike';
        }

        if ($record->status == 'process') {
            return 'new';
        }

        if ($record->status == 'received') {
            return 'safe';
        }

        if ($record->status == 'transit') {
            return 'frozen';
        }

        if ($record->status == 'pickup') {
            return 'positive';
        }

        if ($record->status == 'failed') {
            return 'negative';
        }
    }

    public function listExtendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;
        if ($user->isSuperUser()) {
        } else if (isset($branch)) {
            $query->where('branch_code', $branch->code);
        } else {
            $query->where('branch_code', '-1');
        }
    }

    public function formExtendQuery($query)
    {

        $user = $this->user;
        $branch = $user->branch;
        if ($user->isSuperUser()) {
        } else if (isset($branch)) {
            $query->where('branch_code', $branch->code);
        } else {
            $query->where('branch_code', '-1');
        }
    }
}
