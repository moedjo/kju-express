
# Debug Plus  
  
This plugin Extend  [Bedard.Debugbar](https://octobercms.com/plugin/bedard-debugbar) and  provide Timeline for all events running on the page. This is helpful  when you want to speed up the time of execution of events.  
  
  
## Installation  
  
Install the plugin via October's plugin marketplace and don't forget to  set `debug` to `true` in `config/app.php`.   
  
  
## Usage  
You can change the `napagency/debugplus/config/config.php` and set the  minimum time to track Event Timeline. An other usage of this plugins is  to track everything you want:   
  
`use NapAgency\DebugPlus\classes\Helper;`    
`$helper = Helper::instance();`   
  
Start Tracking   
`$helper->starTimeline($label);`   
End Tracking  
`$helper->endTimeLine();`  
  
See [Bedard.Debugbar](https://octobercms.com/plugin/bedard-debugbar) for more usage instructions and documentation about the DebugBar.
