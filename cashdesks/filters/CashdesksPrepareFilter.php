<?php

namespace app\modules\cashdesks\filters;

use app\modules\cashdesks\models\Admincash;
use app\modules\cashdesks\models\Pickercash;
use app\modules\cashdesks\models\Accountable;

class CashdesksPrepareFilter extends UserFilter
{
    /**
     * Сейф администратора.
     * @var \app\modules\cashdesks\models\Admincash
     */
    private $admincash;
    
    
    /**
     * Под отчёт.
     * @var \app\modules\cashdesks\models\Cashdesk
     */
    private $accountable;
    
    /**
     * Касса комплектовщика.
     * @var \app\modules\cashdesks\models\Pickercash
     */
    private $pickercash;
    
    
    public function beforeAction($action) 
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        $admincash = $this->getAdmincash();
        if ($admincash === null) {
            try {
                Admincash::createCashdesksForDepartment($this->departmentId);
            } catch (\Exception $e) {
                throw $e; 
            }
        }
        
        return true;
    }
    

    public function getAdmincash()
    {
        if ($this->admincash === null) {
            $this->admincash = Admincash::find()
                ->where(['depart_id' => $this->getDepartmentId()])
                ->with(['banknotes'])
                ->one();
        }
        
        return $this->admincash;
    }
    
    public function getPickercash()
    {
        if ($this->pickercash === null) {
            $this->pickercash = Pickercash::find()
                ->where(['depart_id' => $this->getDepartmentId()])
                ->with(['banknotes'])
                ->one();
        }
        
        return $this->pickercash;
    }
    
    public function getAccountable()
    {
       if ($this->accountable === null) {
            $this->accountable = Accountable::find()
                ->where(['depart_id' => $this->getDepartmentId()])
                ->one();
        }
        
        return $this->accountable; 
    }
    
}