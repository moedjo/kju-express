<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use Backend\Models\User;
use BackendMenu;
use Kju\Express\Models\Branch;

class Transactions extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];

    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'access_transactions'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'balances', 'transactions');
    }

    public function index_onDelete()
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }

    private function extendQuery($query)
    {
        $user = $this->user;
        $branch = $user->branch;
        if ($user->hasPermission('access_master_transactions')) {
            return $query;
        }
        if (isset($branch)) {
            return $query
                ->where('transactionable_id', $branch->id)
                ->where('transactionable_type', Branch::class);
        }
        return $query
            ->where('transactionable_id', $user->id)
            ->where('transactionable_type', User::class);
    }

    public function listExtendQuery($query)
    {
        return $this->extendQuery($query);
    }
}
