<?php

namespace app\modules\orders\helpers;

class TimeHelper
{
    const DB_TIME_FORMAT = 'H:i:s';
    
    public static function convertTime($timeStr, $fromFormat, $toFormat)
    {
        $dateTime = self::getDateTimeFromString($timeStr, $fromFormat);
        return $dateTime->format($toFormat);
    }
    
    public static function convertTimeToDbFormat($timeStr, $format = 'H:i:s') 
    {
        $dateTime = self::getDateTimeFromString($timeStr, $format);
        return $dateTime->format(self::DB_TIME_FORMAT);
    }
    
    public static function convertTimeFromDbFormat($timeStr, $format = 'H:i:s')
    {
        $dateTime = self::getDateTimeFromString($timeStr, self::DB_TIME_FORMAT);
        return $dateTime->format($format);
    }
   
    private static function getDateTimeFromString($timeStr, $format = 'H:i:s')
    {
        return \DateTime::createFromFormat($format, $timeStr);
    }
    
    public static function getTimeDbFormatFromTimestamp($timestamp)
    {
        return self::getTimeStringFromTimestamp($timestamp, self::DB_TIME_FORMAT);
    }
    
    
    public static function getTimeStringFromTimestamp($timestamp, $format = 'H:i:s') 
    {
        return (new \DateTime())->setTimestamp($timestamp)->format($format);
    }
    
    private function __construct()
    {
        
    }
    
}