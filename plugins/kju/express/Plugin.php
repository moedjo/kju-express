<?php

namespace Kju\Express;

use Backend\Models\User;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Kju\Express\Classes\AftershipHelperManager;
use Kju\Express\Classes\BalanceHelperManager;
use Kju\Express\Facades\AftershipHelper;
use Kju\Express\Facades\BalanceHelper;
use Kju\Express\Models\IntDeliveryOrder;
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

    public function registerSchedule($schedule)
    {

        $schedule->call(function () {

            $data = array();
            trace_log('running schedule ' .date('Y-m-d'));
            trace_sql();
            $data = DB::table('kju_express_int_delivery_orders')
                ->select('tracking_number',DB::raw("'sf-express'"))
                ->where(DB::raw("DATE(updated_at)"),date('Y-m-d'))
                ->where('status','export')
                ->where('tracking_number', 'like', 'SF%')
                ->get()->toArray();

           
            $array = json_decode(json_encode($data), true);
            array_unshift($array , ['tracking_number'=>'tracking_number','sf-express'=>'courier']);
            Storage::disk('local')
                ->put('media/csv/int-orders.csv',  $this->array2csv($array));
        // })->everyMinute();

        })->dailyAt('19:00');
    }

    function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
    {
        $f = fopen('php://memory', 'r+');
        foreach ($data as $item) {
            fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
        }
        rewind($f);
        return stream_get_contents($f);
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

        App::singleton('balance.helper', function () {
            return new BalanceHelperManager();
        });

        $alias->alias('AftershipHelper', AftershipHelper::class);
        App::singleton('aftership.helper', function () {
            return new AftershipHelperManager();
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
            $model->addDynamicMethod('getDisplayNameAttribute', function ($value) use ($model) {
                return "{$model->login}";
            });
        });
    }
}
