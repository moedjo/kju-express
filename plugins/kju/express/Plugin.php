<?php

namespace Kju\Express;

use Backend\Models\User;
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
    }

    public function registerListColumnTypes()
    {
        return [
            'currency' => function ($value) {
                return number_format($value);
            }
        ];
    }


    public function boot()
    {
        $this->extendBackendUser();
    }

    protected function extendBackendUser()
    {
        User::extend(function ($model) {
            $model->belongsTo['branch'] =  ['Kju\Express\Models\Branch', 'key' => 'branch_code'];
        });
    }
}
