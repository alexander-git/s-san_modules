<?php

namespace app\modules\cashdesks\models;

use Yii;

use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Department;

use app\modules\cashdesks\models\Banknotes;
use app\modules\cashdesks\models\Pickercash;
use app\modules\cashdesks\models\PickercashTransact;

class CashdesksApi
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
     * @return integer ID Подразделения.
     */
    public static function getCurrentUserDepartmentId()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        return Yii::$app->user->identity->depart_id;
    }
    
    /**
     * @return string Имя пользователя.
     */
    public  static function getUserName($userId)
    {
        $user = User::findById($userId);
        if ($user === null) {
            return null;
        } 
        return $user->shortName;   
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
     * @return array Список Администраторов используется в глобальной истории
     *      AdmincashTransact или у бухгалтера. 
     */
    public static function getAdminstratorsList() 
    {
       $users = User::findByCondition([
           'role' => 'administrator' 
       ]);
       return ArrayHelper::map($users, 'id', 'shortName');
    }
        
    /**
     * @param integer $departmentId
     * @return array Список администраторв для конкретного подразделения 
     *      id которого может быть administrator_id AdmincashTransact. 
     *      Используется в GridView.
     */
    public static function getAdministratorsListByDepartmentId($departmentId) {
        $users = User::findByCondition([
            'depart_id' => $departmentId,
            'role' => 'administrator',
        ]);
        return ArrayHelper::map($users, 'id', 'shortName');
    }
    
    /**
     * @param integer $departmentId
     * @return array Список комплектовщиков.
     */
    public static function getPickersList()
    {
        $pickers =  User::findByCondition([
            'role' => 'picker', 
        ]);
        
        return ArrayHelper::map($pickers, 'id', 'shortName');
    }
    
    /**
     * @param integer $departmentId
     * @return array Список комплектовщиков для конкретного подразделения 
     *      Используется в GridView администратора при приёме переводов от 
     *      комплектовщика. 
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
     * @param integer $departmentId
     * @return array Список курьеров для конкретного подразделения 
     *      Используется при приёме денег от курьеров в кассе комплектовщика и 
     *      выдаче под отчёт курьеру в кассе "под отчёт".
     */
    public static function getCouriersListByDepartmentId($departmentId) 
    {
        $couriers =  User::findByCondition([
            'role' => 'courier', 
            'depart_id' => $departmentId
        ]);
        
        return ArrayHelper::map($couriers, 'id', 'shortName');
    }
    
    
    
    /**
     * @param integer $departmentId
     * @return array Список пользователей для  конкретного подразделения
     *      которым можно выплачивать зарплату.
     */
    public static function getUsersListForSalaryPaymnetByDepartmentId($departmentId)
    {
        $users = User::findByConditionExceptSuppliers([
            'depart_id' => $departmentId
        ]); 
        
        return ArrayHelper::map($users, 'id', 'shortName');
    }
      
    /**
     * @param integer $departmentId
     * @return array Список поставщиков для конкртеного подразделения. 
     *      Если он общий можно игнорировать department_id.
     */
    public static function getSuppliersListByDepartmentId($departmentId)
    {
        $suppliers = User::findByCondition([
            'depart_id' => $departmentId,
            'role' => 'supplier',
        ]);
        return ArrayHelper::map($suppliers, 'id', 'name');
    }
    
    /**
     * @param integer $departmentId
     * @return array Cписок пользователей для конкретного подразделения 
     *      которых можно указывать при дополнительных расходах.
     */
    public static function getUsersListForCustomExpenseByDepartmentId($departmentId)
    {
        $users = User::findByConditionExceptSuppliers([
            'depart_id' => $departmentId
        ]); 
        
        return ArrayHelper::map($users, 'id', 'shortName');
    }
    
    /**
     * @param integer $departmentId
     * @return array Список пользователей для конкртеного подразделения которым
     *      можно давать деньги под отчёт.
     */
    public static function getUsersListForAcctabByDepartmentId($departmentId)
    {
        $users = User::findByConditionExceptSuppliers([
            'depart_id' => $departmentId
        ]); 
        
        return ArrayHelper::map($users, 'id', 'shortName');
    }
   
    
    /**
     * @param integer $departmentId
     * @return array Список пользователей для конкретного подразделения 
     *      id которого может быть в user_id AdmincashTransact.
     *      Используется в GridView. Для простоты можно возвращать список всех 
     *      пользователей подразделения.
     */
    public static function getUsersListForAdmincashTransactByDepartmentId($departmentId)
    {
        $users = User::findByCondition([
            'depart_id' => $departmentId,
        ]);

        return ArrayHelper::map($users, 'id', 'shortName');
    }
  
    /**
     * @param integer $departmentId
     * @return array Список пользователей для конкретного подразделения 
     *      id которого может быть в user_edit_id AdmincashTransact. 
     *      Используется в GridView. Для простоты можно возвращать список всех 
     *      пользователей подразделения кроме поставщиков.
     */
    public static function getUsersEditListForAdmincashTransactByDepartmentId($departmentId)
    {
        $administrators = User::findByCondition([
            'depart_id' => $departmentId,
            'role' => 'administrator',
        ]);
        $pickers = User::findByCondition([
            'depart_id' => $departmentId,
            'role' => 'picker',
        ]);
        $buhgalters = User::findByCondition([
            'role' => 'buhgalter',
        ]);
        
        $all = ArrayHelper::merge(
            $administrators, 
            $pickers, 
            $buhgalters
        );
        
        return ArrayHelper::map($all, 'id', 'shortName');
    }
    
    /**
     * @param integer $departmentId
     * @return array Список пользователей для конкретного подразделения
     *      id которых может быть в user_id PickercashTranscat. Т.е. курьеры и
     *      администраторы.  
     */
    public static function getUsersListForPickercashTransactByDepartmentId($departmentId)
    {
        $couriers =  User::findByCondition([
            'role' => 'courier', 
            'depart_id' => $departmentId
        ]);
        $administrators = User::findByCondition([
            'role' => 'administrator',
            'depart_id' => $departmentId,
        ]);
        
        $all = ArrayHelper::merge(
            $couriers,
            $administrators
        );
        
        return ArrayHelper::map($all, 'id', 'shortName');
    }
    

    
    /**
     * @param integer $departmentId
     * @return array Список пользователей для конкретного подразделения
     *      id которых может быть в user_id AccountableTranscat. Т.е. курьеры и
     *      администраторы, которые могли пополнять кассу или изымать деньги из
     *      неё.  
     */
    public static function getUsersListForAccountableTransactByDepartmentId($departmentId)
    {
        $couriers =  User::findByCondition([
            'role' => 'courier', 
            'depart_id' => $departmentId
        ]);
        $administrators = User::findByCondition([
            'role' => 'administrator',
            'depart_id' => $departmentId,
        ]);
        
        $all = ArrayHelper::merge(
            $couriers,
            $administrators
        );
        
        return ArrayHelper::map($all, 'id', 'shortName');
    }
    
    
    /**
     * @return array Список пользователей id которого может быть в user_id 
     *      AdmincashTransact. Используется в глобальноый истории. 
     *      Для простоты можно возвращать список всех пользователей.
     */
    public static function getUsersListForAdmincashTransact()
    {
        $users = User::findByCondition([]);

        return ArrayHelper::map($users, 'id', 'shortName');
    }
    /**
     * @param integer $departmentId
     * @return array Список пользователей для id которого может быть в 
     *      user_edit_id AdmincashTransact.  Используется в глобальноый истории. 
     *      Для простоты можно возвращать список всех пользователей  кроме 
     *      поставщиков.
     */
    public static function getUsersEditListForAdmincashTransact()
    {
        $administrators = User::findByCondition([
            'role' => 'administrator',
        ]);
        $pickers = User::findByCondition([
            'role' => 'picker',
        ]);
        $buhgalters = User::findByCondition([
            'role' => 'buhgalter',
        ]);
        
        $all = ArrayHelper::merge(
            $administrators, 
            $pickers, 
            $buhgalters
        );
        
        return ArrayHelper::map($all, 'id', 'shortName');
    }
    

    /**
     * @param integer $departmentId
     * @return array Список пользователей id которых может быть в user_id 
     *    PickercashTranscat. Т.е. курьеры и администраторы. Используется 
     *    в глобальноый истории.
     */
    public static function getUsersListForPickercashTransact()
    {
        $couriers =  User::findByCondition([
            'role' => 'courier', 
        ]);
        $administrators = User::findByCondition([
            'role' => 'administrator',
        ]);
        
        $all = ArrayHelper::merge(
            $couriers,
            $administrators
        );
        
        return ArrayHelper::map($all, 'id', 'shortName');
    }
    
    /**
     * @param integer $departmentId
     * @return array Список пользователей id которых может быть в user_id
     *      AccountableTranscat. Используется в глобальноый истории.
     */
    public static function getUsersListForAccountableTransact()
    {
        $couriers =  User::findByCondition([
            'role' => 'courier', 
        ]);
        $administrators = User::findByCondition([
            'role' => 'administrator',
        ]);
        
        $all = ArrayHelper::merge(
            $couriers,
            $administrators
        );
        
        return ArrayHelper::map($all, 'id', 'shortName');
    }
    
    /**
     * @param integer $departmentId id подразделения.
     * @param integer $pickerId - id комплектовщика.
     * @param array $values - массив с ключами 
     * count_5000, count_1000, count_500, count_100, count_50 и rest.
     * Не обязательно задавать все ключи в массиве $values.
     * @param string $desc описание операции.
     * @param integer userId - id курьера. null для самовывоза.
     * @return boolean успешна ли операция. Нужно учитывать, что если все значения 
     * равны 0 (и реального изменения не происходит) вернёт false.
     */
    public static function replenPickercash(
        $departmentId, 
        $pickerId,
        $values,
        $desc,
        $userId = null
    ) {
        $pickercash = Pickercash::findOne(['depart_id' => $departmentId]);
        if ($pickercash === null) {
            throw  new \Excpetion('Касса комплектовщика не найдена');
        }
        $pickercashTransact = new PickercashTransact();
        $banknotes = new Banknotes();
        $pickercashTransact->scenario = PickercashTransact::SCENARIO_REPLEN_CREATE;
        $banknotes->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $pickercashTransact->desc = $desc;
        
        $banknotes->count_5000 = 0;
        $banknotes->count_1000 = 0;
        $banknotes->count_500 = 0;
        $banknotes->count_100 = 0;
        $banknotes->count_50 = 0;
        $banknotes->rest = 0;
        foreach ($values as $key => $value) {
            $banknotes->$key = $value;
        }
        
        if ($userId !== null) {
            $pickercashTransact->user_id = $userId;
            
            $success = PickercashTransact::createReplenCourier(
                $pickercashTransact, 
                $pickerId, 
                $pickercash, 
                $banknotes
            );
        } else {
            $success = PickercashTransact::createReplenPickup(
                $pickercashTransact, 
                $pickerId, 
                $pickercash, 
                $banknotes
            );
        }
        
        return $success;
    }
         
    private function __construct()
    {
        
    }
    
}