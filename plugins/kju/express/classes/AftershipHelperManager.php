<?php

namespace Kju\Express\Classes;

use Kju\Express\Models\Settings;
use October\Rain\Network\Http;

class AftershipHelperManager
{
    public function track($slug = "", $tracking_number = "")
    {

        $url = "https://api.aftership.com/v4/trackings/$slug/$tracking_number";
        $result =  Http::get(
            $url,
            function ($http) {
                $aftership_api_key = Settings::get('aftership_api_key');;
                $http->header('aftership-api-key', $aftership_api_key);
                $http->timeout(20);
            }
        );

        if ($result->code == 200) {
            $body = json_decode($result->body);
            return $body->data
                ->tracking
                ->checkpoints;
        } else {
            trace_log($result->body);
        }
        return [];
    }
}
