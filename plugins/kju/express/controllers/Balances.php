<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use Backend\Models\User;
use BackendMenu;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Kju\Express\Facades\BalanceHelper;
use Kju\Express\Models\Branch;

class Balances extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'access_balances'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'balances', 'balances');

    }

    public function index_onDelete()
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }
    public function update_onDelete($recordId = null)
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }

    public function create($context = null)
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }

    public function preview($recordId = null, $context = null)
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }

    private function extendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;
        if ($user->hasPermission('access_master_balances')) {
            return $query;
        }
        if (isset($branch)) {
            return $query
                ->where('owner_id', $branch->id)
                ->where('owner_type', Branch::class);
        }
        return $query
            ->where('owner_id', $user->id)
            ->where('owner_type', User::class);
    }

    public function listExtendQuery($query)
    {
        return $this->extendQuery($query);
    }

    public function formExtendQuery($query)
    {
        return $this->extendQuery($query);
    }
}
