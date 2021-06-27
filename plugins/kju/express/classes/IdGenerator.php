<?php

namespace Kju\Express\Classes;

class IdGenerator
{

    public static function alpha($salt = 'default', $len = 8)
    {

        $hex = md5($salt . uniqid("", true));
        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);
        $uid = preg_replace("#(*UTF8)[^A-Z]#", "", $tmp);
        $len = max(4, min(128, $len));

        while (strlen($uid) < $len)
            $uid .= IdGenerator::alpha(22);

        return substr($uid, 0, $len);
    }

    public static function numeric($salt = 'default', $len = 8)
    {
        $hex = md5($salt . uniqid("", true));
        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);
        $uid = preg_replace("#(*UTF8)[^0-9]#", "", $tmp);
        $len = max(4, min(128, $len));

        while (strlen($uid) < $len)
            $uid .= IdGenerator::numeric(22);

        return substr($uid, 0, $len);
    }
}
