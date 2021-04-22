<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Kju\Express\Models\IntDeliveryOrder;
use Renatio\DynamicPDF\Classes\PDF;

class IntDeliveryOrders extends Controller
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
        'access_int_delivery_orders'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'international', 'int-delivery-orders');
    }

    public function create($context = null)
    {

        if ($this->user->isSuperUser()) {
        } else if ($this->user->hasPermission('is_checker')) {
            return Response::make(View::make('cms::404'), 404);
        } else if ($this->user->hasPermission('is_tracker')) {
            return Response::make(View::make('cms::404'), 404);
        }

        return $this->asExtension('FormController')->create($context);
    }

    public function print($code)
    {
        $delivery_order = IntDeliveryOrder::findOrFail($code);
        $result = ['delivery_order' => $delivery_order];
        return PDF::loadTemplate('int_delivery_order', $result)->stream();
    }

    public function printWithoutPrice($code)
    {
        $delivery_order = IntDeliveryOrder::findOrFail($code);
        $result = ['delivery_order' => $delivery_order];
        return PDF::loadTemplate('int_delivery_order_without_price', $result)->stream();
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

    public function extendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;

        if ($user->isSuperUser()) {
            return $query;
        }

        if ($this->user->hasPermission('is_checker')) {
            return $query
                ->whereIn('status', ['pending', 'process', 'reject']);
        }

        if ($this->user->hasPermission('is_tracker')) {
            return $query
                ->whereIn('status', ['process', 'export']);
        }

        if (isset($branch)) {
            return $query->where('branch_id', $branch->id);
        }

        return $query->where('created_user_id',  $user->id);
    }

    public function listExtendQuery($query)
    {
        $this->extendQuery($query);
    }

    public function formExtendQuery($query)
    {
        $this->extendQuery($query);
    }

    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->trashed()) {
            return 'strike';
        }

        if ($record->status == 'process') {
            return 'new';
        }

        if ($record->status == 'paid') {
            return 'safe';
        }

        if ($record->status == 'return') {
            return 'frozen';
        }

        if ($record->status == 'export') {
            return 'positive';
        }

        if ($record->status == 'unpaid') {
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

            $fields['goods_weight']->disabled = true;
            $fields['goods_height']->disabled = true;
            $fields['goods_width']->disabled = true;
            $fields['goods_length']->disabled = true;

            $fields['payment_method']->disabled = true;
            $fields['total_cost_agreement']->hidden = true;
            if ($this->user->hasPermission('is_checker') && $model->status == 'pending') {

                $fields['goods_type']->disabled = false;
                $fields['goods_description']->disabled = false;
                $fields['goods_amount']->disabled = false;
 
                $fields['goods_weight']->disabled = false;
                $fields['goods_height']->disabled = false;
                $fields['goods_width']->disabled = false;
                $fields['goods_length']->disabled = false;

                $fields['vendor']->disabled = false;
                $fields['checker_comment']->disabled = false;

                $fields['_vendor']->hidden = true;
                $fields['total_cost_agreement']->hidden = false;
            } else  if ($this->user->hasPermission('is_checker') && $model->status != 'pending') {

                $host->removeField('checker_action');
                $host->removeField('vendor'); // recordfinder can't support disabled
                $fields['_vendor']->hidden = false; // recordfinder can't support disabled


            }

            if ($this->user->hasPermission('is_tracker') && $model->status == 'process') {
                $fields['tracking_number']->disabled = false;
                
            } else if ($this->user->hasPermission('is_tracker') && $model->status != 'process') {
                $host->removeField('tracker_action');
            }
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
                'regex:/^\+?\d*$/',
            ];
            $model->rules['consignee_postal_code'] = 'required';

            // Goods Data
            $model->rules['goods_type'] = 'required';
            $model->rules['goods_amount'] = 'required|numeric|min:1';
            $model->rules['goods_weight'] = 'required|numeric|min:1';
            $model->rules['goods_height'] = 'required|numeric|min:1';
            $model->rules['goods_width'] = 'required|numeric|min:1';
            $model->rules['goods_length'] = 'required|numeric|min:1';

            // Panel Data
            $model->rules['goods_weight'] = "required|numeric|min:0";
            $model->rules['total_cost'] = "required|numeric|min:1";
            $model->rules['total_cost_agreement'] = 'in:1';
        });
    }

    public function formBeforeUpdate($model)
    {
        $model->bindEvent('model.beforeValidate', function () use ($model) {
            if ($this->user->hasPermission('is_checker') && $model->status == 'pending') {

                // Goods Data
                $model->rules['goods_type'] = 'required';
                $model->rules['goods_amount'] = 'required|numeric|min:1';
                $model->rules['goods_weight'] = 'required|numeric|min:1';
                $model->rules['goods_height'] = 'required|numeric|min:1';
                $model->rules['goods_width'] = 'required|numeric|min:1';
                $model->rules['goods_length'] = 'required|numeric|min:1';

                // Panel Data
                $model->rules['goods_weight'] = "required|numeric|min:0";
                $model->rules['total_cost'] = "required|numeric|min:1";
                $model->rules['total_cost_agreement'] = 'in:1';

                $model->rules['vendor'] = 'required';
                $model->rules['checker_action'] = 'required';
            }

            if ($this->user->hasPermission('is_tracker') && $model->status == 'process') {
                $model->rules['tracking_number'] = 'required';
                $model->rules['tracker_action'] = 'required';
            }
        });
    }
}
