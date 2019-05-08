<?php

namespace app\modules\picker\models;

use Yii;

use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Department;

class PickerApi
{
    
    public static function getDefaultDepartmentId() 
    {
        return 0;
    }
    
    public static function getDefaultDepartmentName()
    {
        return 'По умолчанию';
    }
    
    public static function getCurrentTimestamp() 
    {
        return time();
    }
    
    
    public static function getDepartmentName($departmentId)
    {
        if ($departmentId === self::getDefaultDepartmentId()) {
            return self::getDefaultDepartmentName();
        }
        
        return Department::findById($departmentId)->name;
    }
    
    public static function getDepartmentsList()
    {
        $departments = Department::findAll();
        return ArrayHelper::map($departments, 'id', 'name');
    }
    
    public static function getCurrentUserId() 
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        return Yii::$app->user->identity->id;
    }
    
    public static function getCurrentUserDepartmentId()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        return Yii::$app->user->identity->depart_id;
    }
    
    public static function getCouriersList($departmentId, $excludedIds = [])
    {
        $couriers =  User::findByCondition(
            ['role' => 'courier', 'depart_id' => $departmentId], 
            $excludedIds
        );
        
        return ArrayHelper::map($couriers, 'id', 'shortName');
    }
    
    public static function getPickersList($ids) 
    {
        $pickers = User::findByIdsList($ids);
        return ArrayHelper::map($pickers, 'id', 'shortName');
    }
    
    public static function getCourierName($courierId)
    {
        $user = User::findById($courierId);
        if ($user === null) {
            return null;
        } 
        return $user->shortName;       
    }
    
    public static function getCourierPhone($courierId)
    {
        $user = User::findById($courierId);
        if ($user === null) {
            return null;
        } 
        return $user->phone;
    }
    
}