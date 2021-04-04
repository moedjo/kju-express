<?php

namespace Kju\Express;

use Backend\Models\User;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\App;
use Kju\Express\Classes\BalanceHelperManager;
use Kju\Express\Facades\BalanceHelper;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function registerComponents()
    {
        return [
            'Kju\Express\Components\DeliveryCosts' => 'deliverycosts',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Settings',
                'description' => 'Manage based settings.',
                'category'    => 'kju.express::lang.plugin.name',
                'icon'        => 'icon-cog',
                'class'       => 'Kju\Express\Models\Settings',
                'order'       => 500,
                'keywords'    => '',
                'permissions' => ['access_settings']
            ]
        ];
    }

    public function registerListColumnTypes()
    {
        return [
            'currency' => function ($value) {
                return number_format($value);
            }
        ];
    }

    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('BalanceHelper', BalanceHelper::class);

        App::singleton('balance.helper', function() {
            return new BalanceHelperManager();
        });
    }


    public function boot()
    {
        $this->extendBackendUser();
    }

    protected function extendBackendUser()
    {
        User::extend(function ($model) {
            $model->belongsTo['branch'] =  [
                'Kju\Express\Models\Branch'
            ];

            $model->morphOne['balance'] =  [
                'Kju\Express\Models\Balance', 'name' => 'owner'
            ];
            $model->morphMany['transactions'] = [
                'Kju\Express\Models\Transaction',
                'name' => 'transactionable'
            ];
            $model->addDynamicMethod('getDisplayNameAttribute', function($value) use ($model) {
                return "{$model->login}";
            });

        });
    }
}
