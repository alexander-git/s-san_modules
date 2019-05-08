<?php

namespace app\modules\personal\helpers;

class DateTimeHelper 
{
    private function __construct()
    {
        
    }
    
    public static function getTimestampFromString($dateTimeStr, $format = 'd-m-Y H:i:s')
    {
        return \DateTime::createFromFormat($format, $dateTimeStr)->getTimestamp();
    }
    
    public static function getDayBeginFromTimestamp($timestamp) 
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $dateTime->setTime(0, 0, 0);
        return $dateTime->getTimestamp();
    }
    
    public static function getDayEndFromTimestamp($timestamp)
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $dateTime->setTime(23, 59, 59);
        return $dateTime->getTimestamp();
    }
    
}