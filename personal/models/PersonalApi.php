<?php

namespace app\modules\personal\models;

use Yii;

use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Department;
use app\modules\personal\models\Question;

class PersonalApi
{
    /**
     * @return integer метка времени.
     */
    public static function getCurrentTimestamp() 
    {
        return time();
    }
       
    /**
     * @return integer ID пользователся.
     */
    public static function getCurrentUserId() 
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        return Yii::$app->user->identity->id;
    }
    
    /**
     * @param integer $userId 
     * @return string Имя пользователя.
     */
    public static function getUserName($userId)
    {
        $user = User::findById($userId);
        if ($user === null) {
            return null;
        } 
        return $user->shortName;   
    }
    
    /**
     * @return array Список пользователей которые могут создавать/редактировать 
     *      вакаенсии. Используется при выводе списка вакансий. 
     */
    public static function getUsersListForVacancy()
    {
        $users = User::findByCondition([
           'role' => 'administrator' 
       ]);
       return ArrayHelper::map($users, 'id', 'shortName');
    }
    

    /**
     * @return string Имя подразделения.
     */
    public static function getDepartmentName($departmentId)
    {
        return Department::findById($departmentId)->name;
    }
    
    /**
     * @return array Список подразделений.
     */
    public static function getDepartmentsList()
    {
        $departments = Department::findAll();
        return ArrayHelper::map($departments, 'id', 'name');
    }
    
    /**
     * @return array Список городов. Используется при работе с анкетами.
     */
    public static function getCitiesList()
    {
        return [
            'Москва' => 'Москва',
            'Санкт-Петербург' => 'Санкт-Петербург', 
        ];
    }
 
    /**
     * Создание анкеты.
     * @param array $params Массив ключи которого соответствуют полям таблицы.
     *      integer id
     *      string name
     *      integer post_id
     *      string birthday Дата в формате d-m-Y (04-03-2016).
     *      string city
     *      string address
     *      string phone
     *      string work_time - Массив из 'morning', 'day', 'evening'. 
     *          Например, ['morning', 'day'],
     *      integer med_book
     *      integer children
     *      integer smoking
     *      integer about_us_id
     *      string experience 
     *      string hobby
     * 
     * @return \app\modules\personal\models\Question|null Модель анкеты.  
     *      null если анкета не создана.
     */
    public static function createQuestion($params) 
    {
        $model = new Question();
        $model->scenario = Question::SCENARIO_CREATE;
        
        foreach ($params as $key => $value) {
            $model->$key = $value;
        }
        
        if (Question::createQuestion($model)) {
            return $model;
        } else {
            return null;
        }
    }
         
    
    /**
     * @return array Список комплектовщиков.
     */
    public static function getPickersListByDepartmentId($departmentId)
    {
        $pickers =  User::findByCondition([
            'role' => 'picker', 
            'depart_id' => $departmentId
        ]);
        
        return ArrayHelper::map($pickers, 'id', 'shortName');
    }
    
    /**
     * @return array Список курьеров.
     */
    public static function getCouriersListByDepartmentId($departmentId)
    {
        $couriers =  User::findByCondition([
            'role' => 'courier', 
            'depart_id' => $departmentId
        ]);
        
        return ArrayHelper::map($couriers, 'id', 'shortName');
    }
    
}