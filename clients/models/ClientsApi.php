<?php

namespace app\modules\clients\models;

class ClientsApi
{
  
    /**
     * @return array Список городов.
     */
    public static function getCitiesList()
    {
        return [
            1 => 'Москва',
            2 => 'Санкт-Петербург', 
        ];
    }
    
    /**
     * @return string Название города.
     */
    public static function getCityNameById($cityId)
    {
        return self::getCitiesList()[(int) $cityId];
    }
 
}