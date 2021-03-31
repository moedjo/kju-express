<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

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
        BackendMenu::setContext('Kju.Express','balances', 'balances');


        $test = $this->user->hasPermission('is_int_checker');
        trace_log('test'.$test);
    }

    public function index_onDelete(){return;}
    public function update_onDelete($recordId = null){return;}

    public function create($context = null)
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }

    public function preview($recordId = null, $context = null)
    {
        return Response::make(View::make('backend::access_denied'), 403);
    }
}
