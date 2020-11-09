<?php
namespace NapAgency\DebugPLus;

use NapAgency\DebugPlus\classes\Helper;
use System\Classes\PluginBase;

/**
 * Class Plugin for OctoberCMS
 * @package NapAgency\DebugPLus
 */
class Plugin extends PluginBase
{
    public $require = ['Bedard.Debugbar'];

    public function pluginDetails()
    {
        return [
            'name' => 'Debug Plus',
            'description' => 'Tools to get more information about events Timeline in debugbar',
            'author' => 'NAP Agency',
            'icon' => 'icon-leaf'
        ];
    }

    public function boot(){
        $helper = Helper::instance();
        $helper->init();
    }
}
