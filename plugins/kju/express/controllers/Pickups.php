<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

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
            return 'positive';
        }

        if ($record->status == 'transit') {
            return 'frozen';
        }

        if ($record->status == 'pickup') {
            return 'processing';
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
}
