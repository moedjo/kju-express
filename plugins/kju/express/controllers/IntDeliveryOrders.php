<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class IntDeliveryOrders extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
        // 'Backend\Behaviors\ImportExportController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $bodyClass = 'compact-container';

    public $requiredPermissions = [
        'access_int_delivery_orders'
    ];
 
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'international', 'int-delivery-orders');
    }

    public function formExtendModel($model)
    {
        $user = $this->user;
        $branch = $user->branch;
        $context = $this->formGetContext();
        if ($context == 'create') {
            if (isset($branch)) {
                $model->branch = $branch;
                $model->origin_region = $branch->region->parent;
            }
        }
    }

    public function listExtendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;
        if ($user->isSuperUser()) {
            // TODO Nothing
        } else if (isset($branch)) {
            $query->where('branch_code', $branch->code);
        } else {
            $query->where('branch_code', null);
        }
    }

    public function formExtendQuery($query)
    {

        $user = $this->user;
        $branch = $user->branch;
        if ($user->isSuperUser()) {
            // TODO Nothing
        } else if (isset($branch)) {
            $query->where('branch_code', $branch->code);
        } else {
            $query->where('branch_code', null);
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


    public function formExtendFields($host, $fields)
    {
        $user = $this->user;
        $branch = $user->branch;
        $context = $host->getContext();
        $model = $host->model;
 
        if ($context == 'create') {

            if (isset($branch)) {
                $host->removeField('origin_region');
            } else {
                $fields['_origin_region']->hidden = true;
            }
        }

        if ($context == 'update') {
            $host->removeField('origin_region');

            // Consignee Data
            $host->removeField('consignee_region'); // recordfinder can't support disabled
            $fields['_consignee_region']->hidden = false; // recordfinder can't support disabled
            $fields['consignee_name']->disabled = true;
            $fields['consignee_phone_number']->disabled = true;
            $fields['consignee_address']->disabled = true;
            $fields['consignee_postal_code']->disabled = true;


            // Goods Data
            $fields['goods_type']->disabled = true;
            $fields['goods_description']->disabled = true;
            $fields['goods_amount']->disabled = true;
            $fields['goods_height']->disabled = true;
            $fields['goods_width']->disabled = true;
            $fields['goods_length']->disabled = true;

            if ($user->hasPermission('access_payment_data_for_delivery_orders')) {
                if ($model->payment_status == 'paid') {
                    $fields['payment_status']->disabled = true;
                    $fields['payment_description']->disabled = true;
                }
            }

            if ($user->hasPermission('access_discount_for_delivery_orders')) {
                $fields['discount']->disabled = true;
            }
            $fields['payment_type']->disabled = true;
        }
    }

    public function formBeforeCreate($model)
    {
        $model->bindEvent('model.beforeValidate', function () use ($model) {

            // Main Data
            $model->rules['origin_region'] = 'required';

            // Customer Data
            $session_key = post('_session_key');
            $count = $model->customer()->withDeferred($session_key)->count();
            if ($count == 0) {
                $model->rules['customer'] = 'required';
                $model->setValidationAttributeName('customer', 'kju.express::lang.customer.singular');
            }

            // Consignee Data
            $model->rules['consignee_region'] = 'required';
            $model->rules['consignee_name'] = [
                'required',
                'regex:/^[\pL\s\-]+$/u',
            ];
            $model->rules['consignee_address'] = 'required';
            $model->rules['consignee_phone_number'] = [
                'required',
                'regex:/(\()?(\+62|62|0)(\d{2,3})?\)?[ .-]?\d{2,4}[ .-]?\d{2,4}[ .-]?\d{2,4}/',
            ];
            $model->rules['consignee_postal_code'] = 'required|digits:5';

            // Goods Data
            $model->rules['goods_type'] = 'required';
            $model->rules['goods_amount'] = 'required|numeric|min:1';
            $model->rules['goods_height'] = 'required|numeric|min:1';
            $model->rules['goods_width'] = 'required|numeric|min:1';
            $model->rules['goods_length'] = 'required|numeric|min:1';

            // Panel Data
            $model->rules['goods_weight'] = "required|numeric|min:0";
            $model->rules['total_cost'] = "required|numeric|min:1";
            $model->rules['total_cost_agreement'] = 'in:1';

            if ($this->user->hasPermission('access_discount_for_delivery_orders')) {
                $model->rules['discount'] = 'required|numeric|between:0,100';
                $model->rules['discount_agreement'] = 'in:1';
            }
        });
    }

    public function formBeforeUpdate($model)
    {
        $model->bindEvent('model.beforeValidate', function () use ($model) {
        });
    }
}
