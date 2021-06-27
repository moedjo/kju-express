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

    public function track_v2($tracking_number = "")
    {

        $url = "https://api.aftership.com/v4/trackings?keyword=$tracking_number&page=1&limit=1";
        $result =  Http::get(
            $url,
            function ($http) {
                $aftership_api_key = Settings::get('aftership_api_key');;
                $http->header('aftership-api-key', $aftership_api_key);
                $http->timeout(20);
            }
        );

        if ($result->code == 200) {
            //trace_log("result : ".$result->body);
            $body = json_decode($result->body);

            if(empty($body->data->trackings[0])){
                return [];
            }
            return $body->data
                ->trackings[0]->checkpoints;
        } else {
            trace_log("result : ".$result->body);
        }
        return [];
    }

    public function track_tgi($tracking_number = "")
    {

        $url = "https://system.tgiexpress.com/api/v1/process_track_api?api_key=kDXTe4eJ4lQkDMZtSficnxxJiPjDAVNe&referenceNumber=$tracking_number&processMasterCode=shipment_tracking";
        $result =  Http::get(
            $url,
            function ($http) {
                $http->timeout(20);
            }
        );

        if ($result->code == 200) {
            // trace_log($result->body);
            $body = json_decode($result->body);
            return $body[0]->processTimeLineLogsList;
        } else {
            trace_log($result->body);
        }
        return [];
    }


    
    public function track_tlx($tracking_number = ""){

        $url = "https://api-customers.tlx.co.id/track-trace/$tracking_number";

        $result =  Http::get(
            $url,
            function ($http) {
                $http->timeout(20);
            }
        );

        if ($result->code == 200) {
            $body = json_decode($result->body);
            return $body->data->track_trace;
        } else {
            trace_log($result->body);
        }
        return [];
    }
}
