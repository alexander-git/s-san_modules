<?php

namespace app\modules\picker\helpers;

class TotalHelper 
{

    public static function getTotal($models, $fieldName)
    {
        $total = 0;
        
        foreach ($models as $model) {
            $value = null;
            
            if (is_object($model) ) {
                $value = $model->{$fieldName};
            } elseif (is_array ($model)) {
                $value = $model[$fieldName];
            }
            
            if (is_numeric($value)) {
                $total += $value;
            }
        }
        
        return $total;
    }
    

    private function __construct()
    {
        
    }
    
}