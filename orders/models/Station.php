<?php

namespace app\modules\orders\models;

class Station
{
    
    const ID_ROLL = 1;
    const ID_PIZZA = 2;
    const ID_HOT = 3;
    const ID_PICK = 4;
    
    public static function getStationsList()
    {
        return [
            self::ID_ROLL => 'Роллы',
            self::ID_PIZZA => 'Пиццы',
            self::ID_HOT => 'Горячка',
            self::ID_PICK => 'Сборка'
        ];
    }
    
    public static function getStationIds()
    {
        return array_keys(self::getStationsList());
    }
    
    public static function getPickStationId()
    {
        return self::ID_PICK;
    }
    
    public static function getStationNameById($id)
    {
        $stationsList = self::getStationsList();
        if (!isset($stationsList[$id])) {
            return null;
        }
        
        return $stationsList[$id];
    }
    
}