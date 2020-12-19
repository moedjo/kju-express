<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\Redirect;
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


    public function export()
    {
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

    public function formExtendModel($model)
    {
        $user = $this->user;
        $branch = $user->branch;
        $context = $this->formGetContext();
        if ($context == 'create') {
            if (isset($branch)) {
                $model->branch = $branch;
                $model->branch_region = $branch->region;
            }
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
                $host->removeField('branch_region');
            } else {

                $fields['_branch']->hidden = true;
                $fields['_branch_region']->hidden = true;

                $fields['pickup_request']->disabled = true;
                $fields['pickup_request']->hidden = true;
            }
        }

        if ($context == 'update') {

            //Main Data
            $fields['pickup_request']->disabled = true;
            if(empty($branch)){
                $fields['pickup_request']->hidden = true;
            }

            // Consignee Data
            $host->removeField('consignee_region'); // recordfinder can't support disabled
            $fields['_consignee_region']->hidden = false; // recordfinder can't support disabled
            $fields['consignee_name']->disabled = true;
            $fields['consignee_phone_number']->disabled = true;
            $fields['consignee_address']->disabled = true;
            $fields['consignee_postal_code']->disabled = true;

            // Goods Data
            $fields['service']->disabled = true;
            $fields['goods_description']->disabled = true;
            $fields['goods_weight']->disabled = true;
            $fields['goods_amount']->disabled = true;

            $host->removeField('branch_region');

            //panel Data
            $fields['agreement']->hidden = true;

            if ($user->hasPermission('access_payment_data_for_delivery_orders')) {
                if ($model->payment_status == 'paid') {
                    $fields['payment_status']->disabled = true;
                    $fields['payment_description']->disabled = true;
                }
            }

            if ($user->hasPermission('access_discount_for_delivery_orders')) {
                $fields['discount']->disabled = true;
            }

            if ($model->status == 'pickup') {

                // Pickup Data
                $host->removeField('pickup_region'); // recordfinder can't support disabled
                $fields['_pickup_region']->hidden = false; // recordfinder can't support disabled
                $fields['pickup_date']->disabled = true;
                $fields['pickup_address']->disabled = true;
                $fields['pickup_postal_code']->disabled = true;

                if ($user->hasPermission('is_supervisor')) {
                    $fields['service']->disabled = false;
                    $fields['goods_weight']->disabled = false;
                    $fields['goods_description']->disabled = false;
                    $fields['goods_amount']->disabled = false;

                    $fields['pickup_courier']->disabled = false;
                    $fields['pickup_date']->disabled = false;

                    $fields['agreement']->hidden = false;
                } else {
                    $host->removeField('pickup_courier'); // recordfinder can't support disabled
                    $fields['_pickup_courier']->hidden = false; // recordfinder can't support disabled

                }
            }
        }
    }

    public function formBeforeUpdate($model)
    {
        $model->bindEvent('model.beforeValidate', function () use ($model) {
            if ($model->status == 'pickup') {
                if ($this->user->hasPermission('is_supervisor')) {
                    // Goods data
                    $model->rules['service'] = 'required';
                    if (isset($model->service) && $model->service->weight_limit != -1) {
                        $model->rules['goods_weight'] = "required";
                        $model->rules['goods_amount'] = "required";
                    }

                    //Pickup data
                    $model->rules['pickup_courier'] = "required";
                    $model->rules['pickup_date'] = "required|after_or_equal:" . date('Y-m-d') . "|before_or_equal:" . date("Y-m-d", strtotime("+1 week"));

                    //Panel data
                    $model->rules['agreement'] = 'in:1';
                }
            }
        });
    }

    public function formBeforeCreate($model)
    {
        $model->bindEvent('model.beforeValidate', function () use ($model) {

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
            $model->rules['service'] = 'required';
            if (isset($model->service) && $model->service->weight_limit != -1) {
                $model->rules['goods_weight'] = "required";
                $model->rules['goods_amount'] = "required";
            }

            // Pickup Data
            if ($model->pickup_request) {
                $model->rules['pickup_region'] = "required";
                $model->rules['pickup_courier'] = "required";
                $model->rules['pickup_date'] = "required|after_or_equal:" . date('Y-m-d') . "|before_or_equal:" . date("Y-m-d", strtotime("+1 week"));

                $model->rules['pickup_address'] = "required";
                $model->rules['pickup_postal_code'] = "required|digits:5";
                if (isset($model->pickup_region)) {
                    $model->rules['branch_region'] = "required|in:" . substr($model->pickup_region->id, 0, 4);
                }
            } else if ($this->user->hasPermission('access_discount_for_delivery_orders')) {
                $model->rules['discount_agreement'] = 'in:1';
                $model->rules['discount'] = 'required|numeric|between:0,100';
            }

            // Panel Data
            $model->rules['total_cost'] = "required|numeric|min:1";
            $model->rules['agreement'] = 'in:1';
        });
    }
}
