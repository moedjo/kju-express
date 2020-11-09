<?php

namespace NapAgency\DebugPlus\classes;

use \Event;

/**
 * Class Helper
 * @package NapAgency\DebugPlus\classes
 */
class Helper
{
    /**
     * Mode debug enabled or not
     * @var bool
     */
    protected $debug = false;

    /**
     * Define if the plugin is initialized or not
     * @var bool
     */
    protected $initialized = false;

    /**
     * Store events
     * @var array
     */
    protected static $timeline = [];

    /**
     * @var Helper
     */
    protected static $instance;

    public function __construct()
    {
        if (config('app.debug') && function_exists('start_measure') && function_exists('stop_measure')) {
            $this->debug = true;
        }
    }

    public function init()
    {
        if ($this->debug === true && $this->initialized === false) {
            $minTime =  (float)\Config::get('napagency.debugplus::min_time');
            Event::listen('*', function (...$params) use ($minTime){
                static $counter;
                static $eventName;
                static $time;
                static $paramEvent;
                $noLog = false;
                // no trace short event
                $currentTime = microtime(true);
                if (isset($eventName) && isset($time)) {

                    $loadTime = $currentTime - $time;
                    // 1ms
                    if (($loadTime * 1000) < $minTime) $noLog = true;
                    if (!$noLog){
                        if (!isset($counter)) {
                            $counter = 0;
                        }
                        $counter++;

                        switch ($eventName) {
                            case 'Illuminate\Routing\Events\RouteMatched':
                                $eventName.=' - '.$paramEvent[0]->request->fullUrl();
                                break;
                            case 'Illuminate\Foundation\Events\LocaleUpdated':
                                $eventName.=' - '.$paramEvent[0]->locale;
                                break;
                            case 'pages.menuitem.resolveItem':
                                $eventName.=' - '.$paramEvent[0] . ' - ' . $paramEvent[1]->title;
                                break;
                            case 'Illuminate\Database\Events\StatementPrepared':
                                $eventName.=' - '.(string)$paramEvent[0]->statement->queryString;
                                break;
                            case 'cms.theme.extendConfig':
                            case 'cms.theme.extendFormConfig':
                            case 'illuminate.query':
                            case 'translator.beforeResolve':
                                $eventName.=' - '.$paramEvent[0];
                                break;
                            case 'cms.page.beforeRenderPartial':
                            case 'cms.page.renderPartial':
                            case 'cms.page.renderContent':
                            case 'system.assets.beforeAddAsset':
                                $eventName.=' - '.$paramEvent[1];
                                break;
                            default;
                        }
                        $this->specificTimeLine($counter. ' - ' .$eventName,$time,$currentTime);
                    }
                }
                $time = $currentTime;
                $eventName = Event::firing();
                $paramEvent = $params;
                //This Event expect an array as return
                if($eventName == 'rainlab.user.getNotificationVars') return [];
            },99999);
            $this->initialized = true;
        }
    }

    /**
     * Get a cached instance of the default Helper
     *
     * @return Helper
     */
    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Log time to analyse performance
     * @param $label
     */
    public function starTimeline($label)
    {
        if ($this->debug) {
            self::$timeline[] = uniqid('seoTimeLine');
            start_measure(end(self::$timeline), 'DebugPlus - ' . $label);
        }
    }

    /**
     * Stop Loging time to analyse performance
     */
    public function endTimeLine()
    {
        if ($this->debug && self::$timeline) {
            stop_measure(end(self::$timeline));
            array_pop(self::$timeline);
            if (!is_array(self::$timeline)) self::$timeline = [];
        }
    }

    /**
     * Add a specific timeline
     * @param string  $label
     * @param int $start
     * @param int $end
     */
    protected function specificTimeLine($label,$start,$end)
    {
        if ($this->debug) {
            add_measure('DebugPlus - ' .$label,$start,$end);
        }
    }

}
