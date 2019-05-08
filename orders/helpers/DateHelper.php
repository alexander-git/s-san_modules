<?php

namespace app\modules\orders\helpers;

class DateHelper
{
    const DB_DATE_FORMAT = 'Y-m-d';
    
    private static $MONTH_NAMES = [
        'янв',
        'фев',
        'мар',
        'апр',
        'май',
        'июн',
        'июл',
        'авг',
        'сен',
        'окт',
        'ноя',
        'дек',
    ];
    
    
    public static function getDateStringFromTimestamp($timestamp, $format = 'd-m-Y') 
    {
        return (new \DateTime())->setTimestamp($timestamp)->format($format);
    }
    
    public static function convertDate($dateStr, $fromFormat, $toFormat)
    {
        $dateTime = self::getDateTimeFromString($dateStr, $fromFormat);
        return $dateTime->format($toFormat);
    }
    
    public static function convertDateToDbFormat($dateStr, $format = 'd-m-y') 
    {
        $dateTime = self::getDateTimeFromString($dateStr, $format);
        return $dateTime->format(self::DB_DATE_FORMAT);
    }
    
    public static function convertDateFromDbFormat($dateStr, $format = 'd-m-y')
    {
        $dateTime = self::getDateTimeFromString($dateStr, self::DB_DATE_FORMAT);
        return $dateTime->format($format);
    }
    
    public static function getDateDbFormatFromTimestamp($timestamp)
    {
        return self::getDateStringFromTimestamp($timestamp, self::DB_DATE_FORMAT);
    }
    
    public static function getCopy($date)
    {
        $format = 'd-m-Y H:i:s;';
        return \DateTime::createFromFormat($format, $date->format($format));
    }
    
    public static function incDayInDate($date)
    {
        $oneDay = new \DateInterval('P1D');
        $date->add($oneDay);
        return $date;
    }
    
    public static function getMonthShortRussianName($monthNumber)
    {
        return self::$MONTH_NAMES[$monthNumber - 1];
    }
    
    private static function getDateTimeFromString($dateTimeStr, $format = 'd-m-Y')
    {
        return \DateTime::createFromFormat($format, $dateTimeStr);
    }
    
    private function __construct()
    {
        
    }
    
}