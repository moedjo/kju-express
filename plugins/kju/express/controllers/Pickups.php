<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\App;
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
        if ($user->isSuperUser()) {

        } else if (isset($branch)) {
            $query->where('branch_code', $branch->code);
            if ($user->hasPermission([
                'is_courier'
            ])){
                $query->whereIn('status',['pickup','process']);
                $query->where('pickup_courier_user_id',$user->id);
            }
        
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
            if ($user->hasPermission([
                'is_courier'
            ])){
                $query->whereIn('status',['pickup','process']);
                $query->where('pickup_courier_user_id',$user->id);
            }
        
        } else {
            $query->where('branch_code', '-1');
        }
    }
}
