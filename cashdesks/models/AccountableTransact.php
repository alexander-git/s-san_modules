<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_accountable_transact}}".
 *
 * @property integer $id
 * @property integer $depart_id
 * @property integer $type
 * @property integer $sum
 * @property integer $date_create
 * @property integer $picker_id
 * @property integer $user_id
 * @property string $desc
 *
 * @property CashdesksAccountable $accountable
 */
class AccountableTransact extends \yii\db\ActiveRecord
{
    const TYPE_REPLEN = 0;
    const TYPE_RETURN = 1;
    const TYPE_ACCTAB_ISSUE = 2;
    const TYPE_ACCTAB_RETURN = 3;
    const TYPE_ACCTAB_ISSUE_PICKUP = 4;
    const TYPE_ACCTAB_RETURN_PICKUP = 5;
    
    // Слеюющие сценарии используются для обоих случаев(выдачи и возврата).
    // В этом случае тип может быть TYPE_ACCTAB_ISSUE или TYPE_ACCTAB_RETURN. 
    const SCENARIO_ACCTAB_OPERATION_CREATE = 'acctabOperationCreate';
    const SCENARIO_ACCTAB_OPERATION_UPDATE = 'acctabOperationUpdate';
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_accountable_transact}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            [
                'depart_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Accountable::className(), 
                'targetAttribute' => ['depart_id' => 'depart_id']
            ],
            
            ['sum', 'required'],
            ['sum', 'integer', 'min' => 0],
            
            ['type', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
            
            ['picker_id', 'required', 'on' => [
                self::SCENARIO_ACCTAB_OPERATION_CREATE,
                self::SCENARIO_ACCTAB_OPERATION_UPDATE,
            ]],
            ['picker_id', 'integer'],
            
            ['date_create', 'required'],
            ['date_create', 'integer'],
                        
            ['user_id', 'integer'],

        
            ['desc', 'string', 'max' => 255],   
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'depart_id' => 'Департамент',
            'type' => 'Тип',
            'sum' => 'Сумма',
            'date_create' => 'Дата создания',
            'picker_id' => 'Комплектовщик',
            'user_id' => 'Пользователь',
            'desc' => 'Комментарий',
            'pickerName' => 'Комплектовщик'
        ];
    }
   
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_ACCTAB_OPERATION_CREATE] =  [
            'sum',
            'user_id',
            'desc',
            '!depart_id',
            '!type',
            '!date_create',
            '!picker_id',
           
        ];
                
        $scenarios[self::SCENARIO_ACCTAB_OPERATION_UPDATE] =  [
            'sum',
            'user_id',
            'desc'
        ];
        
        return $scenarios;
    }
    
    public static function getTypesArray()
    {
        return [
            self::TYPE_REPLEN => 'Пополнение',
            self::TYPE_RETURN => 'Изьятие',
            self::TYPE_ACCTAB_ISSUE => 'Выдача (курьер)',
            self::TYPE_ACCTAB_RETURN => 'Возврат (курьер)',
            self::TYPE_ACCTAB_ISSUE_PICKUP => 'Выдача (самовывоз)',
            self::TYPE_ACCTAB_RETURN_PICKUP => 'Возврат (самовывоз)'
        ];
    }
    
    public function getTypeName()
    {
        return static::getTypesArray()[$this->type];
    }
    
    public function getIsTypeReplen()
    {
        return $this->type === self::TYPE_REPLEN;
    }
    
    public function getIsTypeReturn() 
    {
        return $this->type === self::TYPE_RETURN;
    }
    
    public function getIsTypeAcctabIssue()
    {
        return $this->type === self::TYPE_ACCTAB_ISSUE;
    }
    
    public function getIsTypeAcctabReturn()
    {
        return $this->type === self::TYPE_ACCTAB_RETURN;
    }
    
    public function getIsTypeAcctabIssuePickup()
    {
        return $this->type === self::TYPE_ACCTAB_ISSUE_PICKUP;
    }
    
    public function getIsTypeAcctabReturnPickup()
    {
        return $this->type === self::TYPE_ACCTAB_RETURN_PICKUP;
    }
    
    public function getIsAcctabCourier()
    {
        return  ($this->isTypeAcctabIssue || $this->isTypeAcctabReturn);
    }
    
    public function getIsAcctabPickup()
    {
        return ($this->isTypeAcctabIssuePickup || $this->isTypeAcctabReturnPickup);
    }
    
    public function getIsAcctab()
    {
        return 
            $this->isTypeAcctabIssue || 
            $this->isTypeAcctabReturn ||
            $this->isTypeAcctabIssuePickup || 
            $this->isTypeAcctabReturnPickup;
    }
    
    public function setUpdateScenario() 
    {
        if ($this->isAcctab) {
            $this->scenario = self::SCENARIO_ACCTAB_OPERATION_UPDATE;
        }
    }
    
    public function getDepartmentName()
    {
        return CashdesksApi::getDepartmentName($this->depart_id);
    }
    
    public function getUserName()
    {
        if ($this->user_id === null) {
            return null;
        }
        return CashdesksApi::getUserName($this->user_id);
    }
    
    public function getPickerName()
    {
        if ($this->picker_id === null) {
            return null;
        }
        return CashdesksApi::getUserName($this->picker_id);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountable()
    {
        return $this->hasOne(Accountable::className(), ['depart_id' => 'depart_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmincashTransact()
    {
        if ($this->isTypeReplen) {
            return AdmincashTransact::find()
                ->where([
                    'type_id' => $this->id,
                    'type' => AdmincashTransact::TYPE_ACCOUNTABLE_REPLEN,
                ]);
        } elseif ($this->isTypeReturn) {
            return AdmincashTransact::find()
                ->where([
                    'type_id' => $this->id,
                    'type' => AdmincashTransact::TYPE_ACCOUNTABLE_RETURN,
                ]); 
        } else {
            return null;
        }
    }
    
    public static function createCourierIssue($accountableTransact, $pickerId, $accountable)
    {
        return self::cretePickerOperation($accountableTransact, $pickerId, $accountable, true, false);
    }
    
    public static function createPickupIssue($accountableTransact, $pickerId, $accountable)
    {
        return self::cretePickerOperation($accountableTransact, $pickerId, $accountable, true, true);
    }
    
    public static function createCourierReturn($accountableTransact, $pickerId, $accountable ) 
    {
        return self::cretePickerOperation($accountableTransact, $pickerId, $accountable, false, false);
    }
    
    public static function createPickupReturn($accountableTransact, $pickerId,  $accountable) 
    {
       return self::cretePickerOperation($accountableTransact, $pickerId, $accountable, false, true);
    }
    
    public static function updateOperation($accountableTransact)
    {
        $transaction = AccountableTransact::getDb()->beginTransaction();
        try {
            if (
                $accountableTransact->isTypeAcctabIssue ||
                $accountableTransact->isTypeAcctabIssuePickup    
            ) {
                if (!self::updateSubOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } elseif (
                $accountableTransact->isTypeAcctabReturn ||
                $accountableTransact->isTypeAcctabReturnPickup 
            ) {
                if (!self::updateAddOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } elseif ($accountableTransact->isTypeReplen) {
                throw new \LogicException();
            } elseif ($accountableTransact->isTypeReturn) {
                throw new \LogicException();
            }
            
            if (!$accountableTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $accountable = $accountableTransact->accountable;
            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function revertOperation($accountableTransact)
    {
        if ($accountableTransact->isTypeReplen || $accountableTransact->isTypeReturn) {
            $admincashTransact = $accountableTransact->admincashTransact;
            return AdmincashTransact::revertOperation($admincashTransact);
        }
        
        $transaction = AccountableTransact::getDb()->beginTransaction();
        try {
            if (
                $accountableTransact->isTypeAcctabIssue ||
                $accountableTransact->isTypeAcctabIssuePickup    
            ) {
                if (!self::revertSubOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } elseif (
                $accountableTransact->isTypeAcctabReturn  ||
                $accountableTransact->isTypeAcctabIssuePickup
            ) {
                if (!self::revertAddOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } elseif ($accountableTransact->isTypeReplen) {
                throw new \LogicException();
            } elseif ($accountableTransact->isTypeReturn) {
                throw new \LogicException();
            }
            
            if (!$accountableTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $accountable = $accountableTransact->accountable;
            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }   
    }
    
    public static function updateReplenOperation($accountableTransact)
    {
        $accountable = $accountableTransact->accountable;
        $previousAccountableTransact = AccountableTransact::findOne(['id' => $accountableTransact->id]); 
               
        
        if (!$accountable->subCurrent($previousAccountableTransact->sum)) {  
            return false;
        }
        
        if (!$accountable->addCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        if (!$accountable->subMaxSum($previousAccountableTransact->sum)) {
            return false;
        }
        
        if (!$accountable->addMaxSum($accountableTransact->sum)) {
            return false;
        }
        
        return true;
    }
    
    public static function updateReturnOperation($accountableTransact)
    {
        $accountable = $accountableTransact->accountable;
        $previousAccountableTransact = AccountableTransact::findOne(['id' => $accountableTransact->id]); 
               
        
        if (!$accountable->addCurrent($previousAccountableTransact->sum)) {  
            return false;
        }
        
        if (!$accountable->subCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        if (!$accountable->addMaxSum($previousAccountableTransact->sum)) {
            return false;
        }
        
        if (!$accountable->subMaxSum($accountableTransact->sum)) {
            return false;
        }
        
        return true;
    }
    
    public static function revertReplenOperation($accountableTransact) 
    {
        $accountable = $accountableTransact->accountable;  
        if (!$accountable->subCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        if (!$accountable->subMaxSum($accountableTransact->sum)) {   
            return false;
        }
        
        return true;  
    }
    
    public static function revertReturnOperation($accountableTransact)
    {
        $accountable = $accountableTransact->accountable;  
        if (!$accountable->addCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        if (!$accountable->addMaxSum($accountableTransact->sum)) {   
            return false;
        }
        
        return true; 
    }
    

    private static function cretePickerOperation(
        $accountableTransact, 
        $pickerId, 
        $accountable,
        $isIssue,
        $isPickup = false
    ) {
        $transaction = AccountableTransact::getDb()->beginTransaction();
        try {
            $time = CashdesksApi::getCurrentTimestamp();
            $accountableTransact->depart_id = $accountable->depart_id;
            $accountableTransact->picker_id = $pickerId;
            $accountableTransact->date_create = $time;
            
            if ($isIssue) {
                if ($isPickup) {
                    $accountableTransact->type = self::TYPE_ACCTAB_ISSUE_PICKUP;
                } else {
                    $accountableTransact->type = self::TYPE_ACCTAB_ISSUE;
                }
            } else {
                if ($isPickup) {
                    $accountableTransact->type = self::TYPE_ACCTAB_RETURN_PICKUP;
                } else {
                    $accountableTransact->type = self::TYPE_ACCTAB_RETURN;
                }
            }
            
            if ($isPickup) {
                // Инчае user_id должно прийти их формы.
                $accountableTransact->user_id = $pickerId;
            }
            
            if (!$accountableTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if ($isIssue) {
                if (!$accountable->subCurrent($accountableTransact->sum)) {
                    $transaction->rollBack();
                    return false;
                }
            } else {
                if (!$accountable->addCurrent($accountableTransact->sum)) {
                    $transaction->rollBack();
                    return false;
                }
            }

            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
     
    private static function updateAddOperation($accountableTransact) 
    {
        $accountable = $accountableTransact->accountable;
        $previousAccountableTransact = AccountableTransact::findOne(['id' => $accountableTransact->id]); 
               
        
        if (!$accountable->subCurrent($previousAccountableTransact->sum)) {  
            return false;
        }
        
        if (!$accountable->addCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        return true;
    }
    
    private static function updateSubOperation($accountableTransact) 
    {
        $accountable = $accountableTransact->accountable;
        $previousAccountableTransact = AccountableTransact::findOne(['id' => $accountableTransact->id]); 
               
        
        if (!$accountable->addCurrent($previousAccountableTransact->sum)) {  
            return false;
        }
        
        if (!$accountable->subCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        return true;
    }
        
    private static function revertAddOperation($accountableTransact) 
    {
        $accountable = $accountableTransact->accountable;  
        if (!$accountable->subCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        return true;  
    }
    
    private static function revertSubOperation($accountableTransact)
    {
        $accountable = $accountableTransact->accountable;  
        if (!$accountable->addCurrent($accountableTransact->sum)) {   
            return false;
        }
        
        return true; 
    }
    
}
