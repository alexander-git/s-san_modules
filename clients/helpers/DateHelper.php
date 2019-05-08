<?php

namespace app\modules\clients\helpers;

class DateHelper
{
    const DB_DATE_FORMAT = 'Y-m-d';
    
    public static function getDateStringFromTimestamp($timestamp, $format = 'd-m-Y') 
    {
        return (new \DateTime())->setTimestamp($timestamp)->format($format);
    }
    
    public static function convertDateToDbFormat($dateTimeStr, $format = 'd-m-y') 
    {
        $dateTime = self::getDateTimeFromString($dateTimeStr, $format);
        return $dateTime->format(self::DB_DATE_FORMAT);
    }
    
    public static function convertDateFromDbFormat($dateTimeStr, $format = 'd-m-y')
    {
        $dateTime = self::getDateTimeFromString($dateTimeStr, self::DB_DATE_FORMAT);
        return $dateTime->format($format);
    }
    
    public static function getDateDbFormatFromTimestamp($timestamp)
    {
        return self::getDateStringFromTimestamp($timestamp, self::DB_DATE_FORMAT);
    }
    
    private static function getDateTimeFromString($dateTimeStr, $format = 'd-m-Y')
    {
        return \DateTime::createFromFormat($format, $dateTimeStr);
    }
    
    private function __construct()
    {
        
    }
    
   
}