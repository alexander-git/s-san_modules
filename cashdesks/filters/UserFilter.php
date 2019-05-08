<?php

namespace app\modules\cashdesks\filters;

use yii\base\ActionFilter;
use app\modules\cashdesks\models\CashdesksApi;

class UserFilter extends ActionFilter 
{
    /**
     * ID департамента текщуго пользователя.
     * @var integer 
     */
    protected $departmentId;
    
    /**
     * ID текщуго пользователя.
     * @var integer 
     */
    protected $userId;
    
        
    public function beforeAction($action) 
    {
        $this->userId = CashdesksApi::getCurrentUserId();
        $this->departmentId = CashdesksApi::getCurrentUserDepartmentId();
        
        return true;
    }
    
    public function getUserId() 
    {
        return $this->userId;
    }
    
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    public function canAdministratorAdmincashTransactAction($admincashTransact)
    {
        if ($admincashTransact->depart_id !== $this->getDepartmentId()) {
            return false;
        }
        
        return true;
    }
      
    public function canBuhgalterAdmincashTransactAction($admincashTransact)
    {
        if (!$admincashTransact->isTypeExpense) {
            return false;
        }
        if (!$admincashTransact->isExpenseAccdep) {
            return false;
        }
        
        return true;
    }
   
    public function canAdministratorPickercashTransactAction($pickercashTransact)
    {
        if ($pickercashTransact->depart_id !== $this->getDepartmentId()) {
            return false;
        }
        
        return true;
    }
   
    public function canPickerPickercashTransactAction($pickercashTransact)
    {
        if ($pickercashTransact->picker_id !== $this->getUserId()) {
            return false;
        }
       
       return true;
    }
    
    public function canPickerAccountableTransactAction($accountableTransact)
    {
        if ($accountableTransact->picker_id !== $this->getUserId()) {
            return false;
        }

        return true;
    }
     
}