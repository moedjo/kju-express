<?php

namespace Kju\Express;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
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
}
