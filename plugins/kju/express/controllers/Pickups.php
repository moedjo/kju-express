<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class Pickups extends Controller
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
        'access_pickups'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'pickup-list');
    }

    public function create()
    {
        return Redirect::back();
    }

    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->status == 'process') {
            return 'new';
        }

        if ($record->status == 'pickup') {
            return 'processing';
        }
    }

    public function listExtendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;
        if (isset($branch) && $user->hasPermission('is_courier')) {
            $query->where('branch_id', $branch->id);
            $query->whereIn('status', ['pickup', 'process']);
            $query->where('pickup_courier_user_id', $user->id);
        } else {
            $query->where('branch_id', null);
            $query->whereIn('status', ['pickup', 'process']);
            $query->where('pickup_courier_user_id', $user->id);
        }
    }

    public function formExtendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;
        if (isset($branch) && $user->hasPermission('is_courier')) {
            $query->where('branch_id', $branch->id);
            $query->whereIn('status', ['pickup', 'process']);
            $query->where('pickup_courier_user_id', $user->id);
        } else {
            $query->where('branch_id', null);
            $query->whereIn('status', ['pickup', 'process']);
            $query->where('pickup_courier_user_id', $user->id);
        }
    }


    public function formExtendFields($host, $fields)
    {
        $user = $this->user;
        $context = $host->getContext();
        $model = $host->model;

        if ($context == 'update') {

            if ($model->status == 'process') {

                $fields['service']->disabled = true;
                $fields['goods_weight']->disabled = true;
                $fields['goods_description']->disabled = true;
                $fields['goods_amount']->disabled = true;

                $fields['agreement']->hidden = true;
            }
        }
    }

    public function formBeforeUpdate($model)
    {
        $model->bindEvent('model.beforeValidate', function () use ($model) {
            if ($model->status == 'pickup') {
                $model->rules['agreement'] = 'in:1';
            }
        });
    }
}
