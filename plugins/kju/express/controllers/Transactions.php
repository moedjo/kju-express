<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

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
        return;
    }
}
