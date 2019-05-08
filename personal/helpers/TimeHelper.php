<?php

namespace app\modules\personal\helpers;

class TimeHelper
{
    const DB_TIME_FORMAT = 'H:i:s';
    const SHORT_TIME_FORMAT = 'H:i:s';    
    
    
    public static function convertTimeToDbFormat($dateTimeStr, $format = 'H:i:s') 
    {
        $dateTime = self::getDateTimeFromString($dateTimeStr, $format);
        return $dateTime->format(self::DB_TIME_FORMAT);
    }
    
    public static function convertTimeFromDbFormat($dateTimeStr, $format = 'H:i:s')
    {
        $dateTime = self::getDateTimeFromString($dateTimeStr, self::DB_TIME_FORMAT);
        return $dateTime->format($format);
    }
   
    private static function getDateTimeFromString($dateTimeStr, $format = 'H:i:s')
    {
        return \DateTime::createFromFormat($format, $dateTimeStr);
    }
    
    private function __construct()
    {
        
    }
    
}