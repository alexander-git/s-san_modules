<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_admincash_transact}}".
 *
 * @property integer $id
 * @property integer $depart_id
 * @property integer $type
 * @property integer $banknotes_id
 * @property integer $date_create
 * @property integer $date_end
 * @property integer $date_edit
 * @property integer $administrator_id
 * @property integer $user_id
 * @property integer $user_edit_id
 * @property integer $state
 * @property string $desc
 * @property integer $type_id
 * @property string $type_value
 *
 * @property Banknotes $banknotes
 */
class AdmincashTransact extends \yii\db\ActiveRecord
{          
    const STATE_CREATED = 0;
    const STATE_ACCEPTED = 1;
    const STATE_REJECTED = 2;
   
    const TYPE_REPLEN = 0;
    const TYPE_EXPENSE = 1;
    const TYPE_ACCTAB = 2;
    const TYPE_CHANGE = 3;
    const TYPE_EXCHANGE = 4;
    const TYPE_ACCOUNTABLE_REPLEN = 5;
    const TYPE_ACCOUNTABLE_RETURN = 6;
    const TYPE_TRANSFER_FROM_PICKERCASH = 7;
    
 
    const SCENARIO_REPLEN_CREATE = 'replenCreate';
    const SCENARIO_REPLEN_UPDATE = 'replenUpdate'; 
    
    const SCENARIO_EXPENSE_CREATE = 'expenseCreate';
    const SCENARIO_EXPENSE_UPDATE = 'expenseUpdate';
    // Сценарии для расхода, в которых пользоватль 
    // должен быть указан обязательно.
    const SCENARIO_EXPENSE_USER_CREATE = 'expenseUserCreate';
    const SCENARIO_EXPENSE_USER_UPDATE = 'expenseUserUpadate';
        
    // Сценарии для корректировки кассы и размена купюр.
    const SCENARIO_CHANGE_ADMINCASH_CREATE = 'changeAdmincashCreate';
    const SCENARIO_CHANGE_ADMINCASH_UPDATE = 'changeAdmincashUpdate';
    
    // Сценарии для работы с выдачей в под отчёт пользователю.
    const SCENARIO_ACCTAB_USER_CREATE = 'acctabUserCreate';
    const SCENARIO_ACCTAB_USER_RETURN = 'acctabUserReturn';
    const SCENARIO_ACCTAB_USER_UPDATE = 'acctabUserUpdate';
    
    // Сценарии для работы с кассой "под отчёт".
    const SCENARIO_ACCOUNTABLE_OPERATION_CREATE = 'accountableOperationCreate';
    const SCENARIO_ACCOUNTABLE_OPERATION_UPDATE = 'accountableOperationUpdate';
    
    // Сценарии для работы с переводом от комплектовщика.
    const SCENARIO_TRNASFER_FROM_PICKERCASH_CREATE  = 'transferFromPickercashCreate';
    const SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE = 'transferFromPickercashUpdate';
    const SCENARIO_TRNASFER_FROM_PICKERCASH_UPDATE_PICKER  = 'transferFromPickercashUpdatePicker';
    const SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE_ADMINISTRATOR = 'transferFromPickercashUpdateAdministrator';

    
    // Сценарии для перевода в бухгалетрию.
    const SCENARIO_EXPENSE_ACCDEP_OPERATION = 'expenseAccdepOperation';
    const SCENARIO_EXPENSE_ACCDEP_UPDATE = 'expenseAccdepUpdate';
    const SCENARIO_EXPENSE_ACCDEP_UPDATE_ADMINISTRATOR = 'expenseAccdepUpdateAdministrator';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_admincash_transact}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            
            ['banknotes_id', 'required'],
            ['banknotes_id', 'integer'],
            [
                'banknotes_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Banknotes::className(),
                'targetAttribute' => ['banknotes_id' => 'id'],
            ],
            
            ['type', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
            
            ['administrator_id', 'required', 'except' => [
                self::SCENARIO_TRNASFER_FROM_PICKERCASH_CREATE,
                self::SCENARIO_TRNASFER_FROM_PICKERCASH_UPDATE_PICKER,
                self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE,
            ]],
            ['administrator_id', 'integer'],
            
            ['type_id', 'required', 'except' => [
                self::SCENARIO_CHANGE_ADMINCASH_CREATE,
                self::SCENARIO_CHANGE_ADMINCASH_UPDATE,
            ]],
            ['type_id', 'integer'],
            
            ['type_value', 'required', 'on' => [
                self::SCENARIO_EXPENSE_CREATE,
                self::SCENARIO_EXPENSE_UPDATE,
                self::SCENARIO_EXPENSE_USER_CREATE,
                self::SCENARIO_EXPENSE_USER_UPDATE,
            ]],
            ['type_value', 'string', 'max' => 255],
            
            
            
            ['date_create', 'required'],
            ['date_create', 'integer'],
            
            ['date_end', 'integer'],
            
            ['date_edit', 'integer'],
            
            ['user_id', 'required', 'on' => [
                self::SCENARIO_EXPENSE_USER_CREATE,                
                self::SCENARIO_EXPENSE_USER_UPDATE,
                self::SCENARIO_ACCTAB_USER_CREATE,
                self::SCENARIO_ACCTAB_USER_UPDATE,
            ]],
            ['user_id', 'integer'],
            
            
            ['user_edit_id', 'integer'],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
            [
                'state', 
                'in', 
                'range' => array_keys(self::getStatesArrayAcctabUser()),
                'on' => self::SCENARIO_ACCTAB_USER_UPDATE,
            ],
            [
                'state', 
                'in', 
                'range' => array_keys(self::getStatesArrayTransferFromPickercashUpdateAdministrator()),
                'on' => self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE_ADMINISTRATOR,
            ],
            
            
            ['desc', 'required', 'on' => [
                self::SCENARIO_REPLEN_CREATE,
                self::SCENARIO_REPLEN_UPDATE,
                
                self::SCENARIO_EXPENSE_CREATE,
                self::SCENARIO_EXPENSE_UPDATE,
                self::SCENARIO_EXPENSE_USER_CREATE,
                self::SCENARIO_EXPENSE_USER_UPDATE,
                
                self::SCENARIO_CHANGE_ADMINCASH_CREATE,
                self::SCENARIO_CHANGE_ADMINCASH_UPDATE,
              
                self::SCENARIO_ACCTAB_USER_CREATE,
                self::SCENARIO_ACCTAB_USER_RETURN,
                
                self::SCENARIO_ACCOUNTABLE_OPERATION_CREATE,
                self::SCENARIO_ACCOUNTABLE_OPERATION_UPDATE,
                
                self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE,
                self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE_ADMINISTRATOR,
            ]],
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
            'banknotes_id' => 'Купюры',
            'type_id' => 'Вид операции',
            'type_value' => 'Цель операции',
            'date_create' => 'Дата создания',
            'date_end' => 'Дата завершения',
            'date_edit' => 'Дата редактирования',
            'administrator_id' => 'Администратор',
            'user_id' => 'Пользователь',
            'user_edit_id' => 'Редактировавший',
            'state' => 'Состояние',
            'desc' => 'Описание',
            'userName' => 'Пользователь',
        ];
    }
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_REPLEN_CREATE] = [
            'type_id',
            'type_value',
            'desc',
            '!depart_id',
            '!banknotes_id',
            '!type',
            '!state',
            '!administrator_id',
            '!user_id',
            '!date_create',
            '!date_end',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_REPLEN_UPDATE] = [
            'type_id',
            'type_value',
            'desc',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_EXPENSE_CREATE] = [
            'user_id',
            'desc',
            'type_value',
            '!type_id',
            '!depart_id',
            '!banknotes_id',
            '!type',
            '!state',
            '!administrator_id',
            '!date_create',
            '!date_end',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_EXPENSE_USER_CREATE] = 
            $scenarios[self::SCENARIO_EXPENSE_CREATE];
        
        $scenarios[self::SCENARIO_EXPENSE_UPDATE] = [
            'user_id',
            'desc',
            'type_value',
            'state',
            '!banknotes_id',
            '!date_end',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_EXPENSE_USER_UPDATE] = 
            $scenarios[self::SCENARIO_EXPENSE_UPDATE];

        
        $scenarios[self::SCENARIO_CHANGE_ADMINCASH_CREATE] = [
            'desc',
            '!banknotes_id',
            '!depart_id',
            '!administrator_id',
            '!type',
            '!state',
            '!type_id',
            '!type_value',
            '!user_id',
            '!date_create',
            '!date_end',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_CHANGE_ADMINCASH_UPDATE] = [
            'desc',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_ACCTAB_USER_CREATE] = [
            'desc',
            'user_id',
            '!banknotes_id',
            '!depart_id',
            '!type',
            '!state',
            '!administrator_id',
            '!type_id',
            '!type_value',
            '!date_create',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_ACCTAB_USER_RETURN] = [
            'desc',
            '!state',
            '!date_end',
            '!date_edit',
            '!user_edit_id',
        ];

        $scenarios[self::SCENARIO_ACCTAB_USER_UPDATE] = [
            'desc',
            'user_id',
            'state',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];

        $scenarios[self::SCENARIO_ACCOUNTABLE_OPERATION_CREATE] = [
            'desc',
            '!banknotes_id',
            '!depart_id',
            '!administrator_id',
            '!type',
            '!state',
            '!type_id',
            '!type_value',
            '!user_id',
            '!date_create',
            '!date_end',
            '!date_edit',
            '!user_edit_id',         
        ];
        
        $scenarios[self::SCENARIO_ACCOUNTABLE_OPERATION_UPDATE] = [
            'desc',
            'state',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_TRNASFER_FROM_PICKERCASH_CREATE] = [
            '!desc',
            '!banknotes_id',
            '!depart_id',
            '!administrator_id',
            '!type',
            '!state',
            '!type_id',
            '!type_value',
            '!user_id',
            '!date_create',
            '!date_end',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE] = [
            'desc',  
            'state',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE_ADMINISTRATOR] = 
            $scenarios[self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE];
        
        $scenarios[self::SCENARIO_TRNASFER_FROM_PICKERCASH_UPDATE_PICKER] = [
            '!desc',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_EXPENSE_ACCDEP_OPERATION] = [
            'desc',
            '!banknotes_id',
            '!state',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_EXPENSE_ACCDEP_UPDATE] = [
            'desc',
            'state',
            '!banknotes_id',
            '!date_edit',
            '!user_edit_id',
        ];
        
        $scenarios[self::SCENARIO_EXPENSE_ACCDEP_UPDATE_ADMINISTRATOR] = [
            'desc',
            '!state',
            '!banknotes',
            '!date_edit',
            '!user_edit_id',
        ];
        
        return $scenarios;
    }
    
    public static function getStatesArray()
    {
        return [
            self::STATE_CREATED => 'Создана',
            self::STATE_ACCEPTED => 'Принята',
            self::STATE_REJECTED => 'Отклонена',
        ];
    }
    
    public static function getStatesArrayAcctabUser()
    {
        $statesArray = self::getStatesArray();
        unset($statesArray[self::STATE_REJECTED]);
        return $statesArray;
    }
    
    public static function getStatesArrayTransferFromPickercashUpdateAdministrator()
    {
        $statesArray = self::getStatesArray();
        unset($statesArray[self::STATE_CREATED]);
        return $statesArray;
    }
    
    public function getStateName()
    {
        return static::getStatesArray()[$this->state];
    }
    
    public function getIsCreated()
    {
        return ((int) $this->state) === self::STATE_CREATED;
    }
    
    public function getIsAccepted()
    {
        return ((int) $this->state) === self::STATE_ACCEPTED;
    }
    
    public function getIsRejected()
    {
        return ((int) $this->state) === self::STATE_REJECTED;
    }
    
    public static function getTypesArray()
    {
        return [
            self::TYPE_REPLEN => 'Пополнение',
            self::TYPE_EXPENSE => 'Расход',
            self::TYPE_ACCTAB => 'Подотчёт',
            self::TYPE_CHANGE => 'Корректировка',
            self::TYPE_EXCHANGE => 'Размен',
            self::TYPE_ACCOUNTABLE_REPLEN => 'Пополнение "Под отчёт"',
            self::TYPE_ACCOUNTABLE_RETURN => 'Изъятие "Под отчёт"',
            self::TYPE_TRANSFER_FROM_PICKERCASH => 'Перевод от комплектовщика',
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
    
    public function getIsTypeExpense()
    {
        return $this->type === self::TYPE_EXPENSE;
    }
    
    public function getIsTypeAcctab()
    {
        return $this->type === self::TYPE_ACCTAB;
    }
    
    public function getIsTypeChange()
    {
        return $this->type === self::TYPE_CHANGE;
    }
    
    public function getIsTypeExchange()
    {
        return $this->type === self::TYPE_EXCHANGE;
    }
    
    public function getIsTypeAccountableReplen()
    {
        return $this->type === self::TYPE_ACCOUNTABLE_REPLEN;
    }
    
    public function getIsTypeAccountableReturn()
    {
        return $this->type === self::TYPE_ACCOUNTABLE_RETURN;
    }
    
    public function getIsTypeTransferFromPickercash()
    {
        return $this->type === self::TYPE_TRANSFER_FROM_PICKERCASH;
    }
    
    public function getIsAccountable()
    {
        return $this->isTypeAccountableReplen || $this->IsTypeAccountableReturn;
    }
    
    public function getIsExpenseAccdep()
    {
        return 
            $this->isTypeExpense && 
            ($this->type_id === ExpenseType::getExpenseTypeAccdepId());
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
    
    public function getUserEditName()
    {
        if ($this->user_edit_id === null) {
            return null;
        }
        return CashdesksApi::getUserName($this->user_edit_id);
    }
    
    public function getAdministratorName()
    {
        return CashdesksApi::getUserName($this->administrator_id);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanknotes()
    {
        return $this->hasOne(Banknotes::className(), ['id' => 'banknotes_id']);
    }
           
    public function getAdmincash() 
    {
        return $this->hasOne(Admincash::className(), ['depart_id' => 'depart_id']);
    }
    
    public function getPickercash() 
    {
        return $this->hasOne(Pickercash::className(), ['depart_id' => 'depart_id']);
    }   
    
    public function getAccountable()
    {
        return $this->hasOne(Accountable::className(), ['depart_id' => 'depart_id']);
    }
        
    public function getExpenseType()
    {
        if ($this->isTypeExpense) {
            return $this->hasOne(ExpenseType::className(), ['id' => 'type_id']);
        } else {
            return null;
        }
    }
    
    public function getReplenType()
    {
        if ($this->isTypeReplen) {
            return $this->hasOne(ReplenType::className(), ['id' => 'type_id']);
        } else {
            return null;
        }
    }
    
    public function getPickercashTransact()
    {
        if ($this->isTypeTransferFromPickercash) {
            return $this->hasOne(PickercashTransact::className(), ['id' => 'type_id']);
        } else {
            return null;
        }
    }
    
    public function getAccountableTransact() 
    {
        if ($this->isTypeAccountableReplen || $this->isTypeAccountableReturn) {
            return $this->hasOne(AccountableTransact::className(), ['id' => 'type_id']);
        } else {
            return null;
        }
    }
    
    
    public function setCreateScenarioOnExpenseType($expenseTypeModel)
    {
        if ($expenseTypeModel->isTypeSalary || $expenseTypeModel->isTypeSupplier) {
            $this->scenario = self::SCENARIO_EXPENSE_USER_CREATE;
        } else {
            $this->scenario = self::SCENARIO_EXPENSE_CREATE;
        }
    }
    
    public function setUpdateScenario($isUpdateByAdministrator = false)
    {
        if ($this->isTypeReplen) {
            $this->scenario = self::SCENARIO_REPLEN_UPDATE;
            return;
        }
        
        if ($this->isTypeExpense) {
            $expenseTypeModel = $this->expenseType;
            if ($expenseTypeModel->isTypeSalary || $expenseTypeModel->isTypeSupplier) {
                $this->scenario = self::SCENARIO_EXPENSE_USER_UPDATE;
            } elseif ($expenseTypeModel->isTypeAccdep && $isUpdateByAdministrator) {
                $this->scenario = self::SCENARIO_EXPENSE_ACCDEP_UPDATE_ADMINISTRATOR;
            } else {
                $this->scenario = self::SCENARIO_EXPENSE_UPDATE;
            }
        }
        
        if ($this->isTypeChange || $this->isTypeExchange) {
            $this->scenario = self::SCENARIO_CHANGE_ADMINCASH_UPDATE;
            return;
        } 
        
        if ($this->isTypeAcctab) {
            if ($this->user_id !== null) {
                $this->scenario = self::SCENARIO_ACCTAB_USER_UPDATE;
                return;
            } else {
                throw new \LogicException();
            }
        }
        
        if ($this->isTypeAccountableReplen || $this->isTypeAccountableReturn) {
            $this->scenario = self::SCENARIO_ACCOUNTABLE_OPERATION_UPDATE;
            return;
        }

        if ($this->isTypeTransferFromPickercash) {
            if ($isUpdateByAdministrator) {
                $this->scenario = self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE_ADMINISTRATOR;
                return;
            } else {
                $this->scenario = self::SCENARIO_TRANSFER_FROM_PICKERCASH_UPDATE;
                return;
            }
        }
    }
    
    public static function createReplen(
        $admincashTransact, 
        $administratorId,
        $admincash,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_REPLEN;
            $admincashTransact->state = self::STATE_ACCEPTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->user_id = null;
            $admincashTransact->date_create = $time;
            $admincashTransact->date_end = $time;        
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
          
            if (!$admincash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function createExpense(
        $admincashTransact, 
        $administratorId,
        $admincash,
        $banknotes,  
        $expenseType
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            if ($expenseType->isTypeAcctab) {
                throw new \InvalidArgumentException('Неверный тип расхода.');
            }
            
            $time  = CashdesksApi::getCurrentTimestamp();
            
            // Делается ли операция в один шаг и сразу считается завершённой.
            // При выдаче в подотчёт это транзакция считается завершённой только
            // когда деньги вернулись.
            $isTransactEnd =  
                $expenseType->isTypeSalary ||
                $expenseType->isTypeBank ||
                $expenseType->isTypeSupplier ||
                $expenseType->isTypeText ||
                $expenseType->isTypeArray;
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }

            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_EXPENSE;
            $admincashTransact->type_id = $expenseType->id;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->date_create = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (
                $expenseType->isTypeSalary  ||        
                $expenseType->isTypeBank ||
                $expenseType->isTypeAccdep
            ) {
                // В других случаях приходит из формы.
                $admincashTransact->type_value = $expenseType->name;
            }
            
            if ($isTransactEnd) {
                $admincashTransact->state = self::STATE_ACCEPTED; 
            } else {
                $admincashTransact->state = self::STATE_CREATED;
            }
            
            if ($expenseType->isTypeBank || $expenseType->isTypeAccdep) {
                $admincashTransact->user_id = null;
            }
            
            if ($isTransactEnd) {
                $admincashTransact->date_end = $time;
            }
                       
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            if (!$admincash->sub($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }        
    }
    
    public static function changeAdmincash(
        $admincashTransact,
        $administratorId,
        $admincash,
        $banknotes 
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_CHANGE;
            $admincashTransact->state = self::STATE_ACCEPTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->user_id = null;
            $admincashTransact->type_id = null;
            $admincashTransact->type_value = null;
            $admincashTransact->date_create = $time;         
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$admincash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }         
    }
    
    public static function exchangeAdmincash(
        $admincashTransact, 
        $administratorId,
        $admincash,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_EXCHANGE;
            $admincashTransact->state = self::STATE_ACCEPTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->user_id = null;
            $admincashTransact->type_id = null;
            $admincashTransact->type_value = null;
            $admincashTransact->date_create = $time;         
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$admincash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function createAcctabUser(
        $admincashTransact,
        $administratorId,
        $admincash, 
        $banknotes  
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $expenseType = ExpenseType::findOne(['type' => ExpenseType::TYPE_ACCTAB]);
            
            if ($expenseType === null) {
                throw new \Exception('Тип расхода не найден.');
            }
            
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }

            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_ACCTAB;
            $admincashTransact->state = self::STATE_CREATED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->type_id = $expenseType->id;
            $admincashTransact->type_value = $expenseType->name;
            $admincashTransact->date_create = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            if (!$admincash->sub($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }        
    }
    
    public static function returnAcctabUser(
        $admincashTransact,
        $administratorId,
        $admincash 
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
           
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $banknotes = $admincashTransact->banknotes;
            
            $admincashTransact->state = self::STATE_ACCEPTED;
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;

            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            if (!$admincash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function createAccountableReplen(
        $admincashTransact,
        $administratorId,
        $admincash,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $accountableTransact = new AccountableTransact();
            $accountableTransact->depart_id = $admincash->depart_id;
            $accountableTransact->type = AccountableTransact::TYPE_REPLEN;
            $accountableTransact->user_id = $administratorId;
            $accountableTransact->sum = $banknotes->sum;
            $accountableTransact->desc = $admincashTransact->desc;
            $accountableTransact->date_create = $time;

            if (!$accountableTransact->save()) {
                $transaction->rollBack();
                return false;
            }

            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_ACCOUNTABLE_REPLEN;
            $admincashTransact->state = self::STATE_ACCEPTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->user_id = null;
            $admincashTransact->type_id = $accountableTransact->id;
            $admincashTransact->type_value = null;
            $admincashTransact->date_create = $time;         
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            if (!$admincash->sub($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $accountable =  $admincash->accountable;
            
            if (!$accountable->addCurrent($banknotes->sum)) {
                $transaction->rollBack();   
                return false;
            }
            
            if (!$accountable->addMaxSum($banknotes->sum)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }  
    }

    public static function createAccountableReturn(
        $admincashTransact,
        $administratorId,
        $admincash,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $accountable =  $admincash->accountable;
        
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
           
            $accountableTransact = new AccountableTransact();
            $accountableTransact->depart_id = $admincash->depart_id;
            $accountableTransact->type = AccountableTransact::TYPE_RETURN;
            $accountableTransact->user_id = $administratorId;
            $accountableTransact->sum = $banknotes->sum;
            $accountableTransact->desc = $admincashTransact->desc;
            $accountableTransact->date_create = $time;

            
            if (!$accountableTransact->save()) {
                $transaction->rollBack();
                return false;
            }

            $admincashTransact->depart_id = $admincash->depart_id;
            $admincashTransact->type = self::TYPE_ACCOUNTABLE_RETURN;
            $admincashTransact->state = self::STATE_ACCEPTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->user_id = null;
            $admincashTransact->type_id = $accountableTransact->id;
            $admincashTransact->type_value = null;
            $admincashTransact->date_create = $time;         
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            $admincashTransact->banknotes_id = $banknotes->id;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            if (!$admincash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            if (!$accountable->subCurrent($banknotes->sum)) {
                $transaction->rollBack();   
                return false;
            }
            
            if (!$accountable->subMaxSum($banknotes->sum)) {
                $transaction->rollBack();   
                return false;
            }
            
            $admincash->check();
            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }  
    }
    
    public static function createTransferFromPickercash(
        $pickercashTransact,
        $pickerId,
        $banknotes,
        $time 
    ) {
        $admincashTransact = new AdmincashTransact();
        $admincashTransact->scenario = self::SCENARIO_TRNASFER_FROM_PICKERCASH_CREATE;
        $admincashTransact->depart_id = $pickercashTransact->depart_id;
        $admincashTransact->type = self::TYPE_TRANSFER_FROM_PICKERCASH;
        $admincashTransact->state = self::STATE_CREATED;
        $admincashTransact->administrator_id = null;
        $admincashTransact->user_id = $pickerId;
        $admincashTransact->type_id = $pickercashTransact->id;
        $admincashTransact->type_value = null;
        $admincashTransact->date_create = $time;         
        $admincashTransact->date_end = null;
        $admincashTransact->date_edit = $time;
        $admincashTransact->user_edit_id = $pickerId;
        $admincashTransact->banknotes_id = $banknotes->id;

        if (!empty($pickercashTransact->desc)) {
            $admincashTransact->desc = $pickercashTransact->desc;
        }
        
        if (!$admincashTransact->save()) {
            return false;
        }

        return true;
    }
    
    public static function acceptAccdep(
        $admincashTransact,
        $buhgalterId,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            if (!self::updateSubOperation($admincashTransact, $banknotes)) {
                $transaction->rollBack();
                return false;
            }
        
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            $admincashTransact->state = AdmincashTransact::STATE_ACCEPTED;
            $admincashTransact->date_end = $time;    
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $buhgalterId;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash = $admincashTransact->admincash;
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }     
    }
    
     public static function rejectAccdep(
        $admincashTransact,
        $buhgalterId
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            if (!self::revertSubOperation($admincashTransact)) {
                $transaction->rollBack();
                return false;
            }
        
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            $admincashTransact->state = AdmincashTransact::STATE_REJECTED;
            $admincashTransact->date_end = $time;    
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $buhgalterId;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
           
            $admincash = $admincashTransact->admincash;
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }     
    }
     
    public static function updateOperation(
        $admincashTransact,
        $userEditId,
        $banknotes = null
    ) {
        if ($admincashTransact->isTypeExpense) {
            $expenseType = $admincashTransact->expenseType;
            if ($expenseType->isTypeAccdep) {
                return self::updateAccdepOperation($admincashTransact, $userEditId, $banknotes);
            }
        }
        
        if ($admincashTransact->isTypeAcctab) {
            return self::updateAcctabUserOperation($admincashTransact, $userEditId, $banknotes);
        }
        
        if (
            $admincashTransact->isTypeAccountableReplen || 
            $admincashTransact->isTypeAccountableReturn
        ) {
            return self::updateAccountableOperation($admincashTransact, $userEditId, $banknotes);
        }
        
        if ($admincashTransact->isTypeTransferFromPickercash) {
            return self::updateTransferFromPickercashOperation($admincashTransact, $userEditId, $banknotes);
        }
        
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            if ($admincashTransact->isTypeReplen) {
                if (!self::updateAddOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                } 
            }
            
            if ($admincashTransact->isTypeExpense) {
                if (!self::updateSubOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
                
            if ($admincashTransact->isTypeChange) {
                if (!self::updateAddOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if ($admincashTransact->isTypeExchange) {
                if (!self::updateAddOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $userEditId;

            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash = $admincashTransact->admincash;
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function revertOperation($admincashTransact)
    {
        if ($admincashTransact->isTypeExpense) {
            $expenseType = $admincashTransact->expenseType;
            if ($expenseType->isTypeAccdep) {
                return self::revertAccdepOperation($admincashTransact);
            }
        }
        
        if ($admincashTransact->isTypeAcctab) {
            return self::revertAcctabUserOperation($admincashTransact);  
        }
        
        if (
            $admincashTransact->isTypeAccountableReplen || 
            $admincashTransact->isTypeAccountableReturn
        ) {
            return self::revertAccountableOperation($admincashTransact);
        }
        
        if ($admincashTransact->isTypeTransferFromPickercash) {
            return self::revertTransferFromPickercashOperation($admincashTransact);
        }
              
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $banknotes = $admincashTransact->banknotes;
   
            if ($admincashTransact->isTypeReplen) {
                if (!self::revertAddOperation($admincashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
            
            if ($admincashTransact->isTypeExpense) {
                if (!self::revertSubOperation($admincashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
            
            if ($admincashTransact->isTypeChange) {
                if (!self::revertAddOperation($admincashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
            
            if ($admincashTransact->isTypeExchange) {
                if (!self::revertAddOperation($admincashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
            
            
            if ($admincashTransact->isTypeAcctab) {
                if ($admincashTransact->isCreated) {
                    if (!self::revertSubOperation($admincashTransact)) {
                        $transaction->rollBack();
                        return false;
                    }
                } elseif ($admincashTransact->isAccepted) {
                    // Деньги уже возвращены в подотчёт ничего делать не нужно.
                }
            }
            
 
            if (!$admincashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$banknotes->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash = $admincashTransact->admincash;
            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
        
    public static function updateTransferFromPickercashByAdministrator(
        $admincashTransact,
        $administratorId
    ) {
        return self::updateTransferFromPickercashOperation($admincashTransact, $administratorId);
    }
    
    private static function updateAccdepOperation(
        $admincashTransact,
        $userEditId,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $admincash = $admincashTransact->admincash;
            $previousAdmincashTransact = AdmincashTransact::findOne(['id' => $admincashTransact->id]);
            $previousBanknotes = Banknotes::findOne(['id' => $previousAdmincashTransact->banknotes_id]);
            
            // Сначала вёрнём кассу в состояние created.
            if ($previousAdmincashTransact->isCreated) {

            }
            if ($previousAdmincashTransact->isAccepted) {

            } 
            if ($previousAdmincashTransact->isRejected) {
                if (!$admincash->sub($previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            // Выполним действие заново.
            if ($admincashTransact->isCreated) {
                if (!self::updateSubOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if ($admincashTransact->isAccepted) {
                if (!self::updateSubOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if ($admincashTransact->isRejected) {
                if (!$admincash->add($previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            $admincashTransact->user_edit_id = $userEditId;
            $admincashTransact->date_edit = $time;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash->check();
              
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }  
    }
    
    private static function updateAcctabUserOperation(
        $admincashTransact,
        $userEditId,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $admincash = $admincashTransact->admincash;
            $previousAdmincashTransact = AdmincashTransact::findOne(['id' => $admincashTransact->id]);
            $previousBanknotes = Banknotes::findOne(['id' => $previousAdmincashTransact->banknotes_id]);
            
            // Сначала вёрнём кассу в состояние created.
            if ($previousAdmincashTransact->isCreated) {

            }
            if ($previousAdmincashTransact->isAccepted) {
                if (!$admincash->sub($previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
     
            // Выполним действие заново.
            if ($admincashTransact->isCreated) {
                if (!self::updateSubOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if ($admincashTransact->isAccepted) {
                if (!$admincash->add($previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
        
            $admincashTransact->user_edit_id = $userEditId;
            $admincashTransact->date_edit = $time;
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
        
            $admincash->check();
              
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }  
    }
    
    private static function updateAccountableOperation(
        $admincashTransact,
        $userEditId,
        $banknotes
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $accountableTransact = $admincashTransact->accountableTransact; 
            $accountableTransact->sum = $banknotes->sum;
            $accountableTransact->desc = $admincashTransact->desc;
            
            if ($admincashTransact->isTypeAccountableReplen) {
                if (!self::updateSubOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
                
                if (!AccountableTransact::updateReplenOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if ($admincashTransact->isTypeAccountableReturn) {
                if (!self::updateAddOperation($admincashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
                
                if (!AccountableTransact::updateReturnOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            }
 
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $userEditId;

            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
                  
            if (!$accountableTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash = $admincashTransact->admincash;
            $accountable = $admincash->accountable;
            $admincash->check();
            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
                
    private static function updateTransferFromPickercashOperation(
        $admincashTransact,
        $userEditId,
        $banknotes = null
    ) {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $pickercashTransact = $admincashTransact->pickercashTransact;
            $admincash = $admincashTransact->admincash;
            $pickercash = $pickercashTransact->pickercash;
            $previousAdmincashTransact = AdmincashTransact::findOne(['id' => $admincashTransact->id]);
            $previousBanknotes = Banknotes::findOne(['id' => $previousAdmincashTransact->banknotes_id]);
            if ($banknotes === null) {
                $banknotes = $previousBanknotes;
            }
            
            // Скопируем состоянии admincashTransact в pickercashTransact.
            self::setPickercashTransactState($pickercashTransact, $admincashTransact);
            
            // Сначала вёрнём кассы в состояние created.
            if ($previousAdmincashTransact->isCreated) {

            } 
            if ($previousAdmincashTransact->isAccepted) {
                if (!$admincash->sub($previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
            if ($previousAdmincashTransact->isRejected) {
                if (!$pickercash->sub($previousBanknotes)) {
                    $transaction->rollBack();
                }
            }
            
            // Выполним действие заново.
            if ($admincashTransact->isCreated) {
                if (!$pickercash->add($previousBanknotes)) {  
                    return false;
                }
                if (!self::updateBanknotes($admincashTransact, $pickercashTransact, $banknotes, $previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
                if (!$pickercash->sub($banknotes)) {   
                    return false;
                }    
            }
            if ($admincashTransact->isAccepted) {
                if (!$pickercash->add($previousBanknotes)) {  
                    return false;
                }
                if (!self::updateBanknotes($admincashTransact, $pickercashTransact, $banknotes, $previousBanknotes)) {
                    $transaction->rollBack();
                    return false;
                }
                if (!$pickercash->sub($banknotes)) {   
                    return false;
                }    
                if (!$admincash->add($banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if ($admincashTransact->isRejected) {
                if (!$pickercash->add($previousBanknotes)) {
                    $transaction->rollBack();
                }
            }
            
            
            if (
                $previousAdmincashTransact->isCreated && 
                ($admincashTransact->isAccepted || $admincashTransact->isRejected) 
            ) {
                // Если транзакия при редактировании меняет состояние с created 
                // на accepted или rejected первый раз, то учётём это действие как
                // приём транзакции или отклонение перевода.
                if ($previousAdmincashTransact->date_end === null) {
                    $admincashTransact->administrator_id = $userEditId;
                    $pickercashTransact->user_id = $userEditId;
                    $admincashTransact->date_end = $time;
                    $pickercashTransact->date_end = $time;          
                }
            }
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash->check();
            $pickercash->check();
              
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }  
    }
    
    /**
     * Метод должен вызываться в рамках транзакции.
     * @return boolean
     */
    private static function updateAddOperation($admincashTransact, $banknotes) 
    {
        $admincash = $admincashTransact->admincash;
        $previousBanknotes = Banknotes::findOne(['id' => $admincashTransact->banknotes_id]); 
                
        if (!$admincash->sub($previousBanknotes)) {  
            return false;
        }
        
        if (!$banknotes->save()) {
            return false;
        }  
                
        if ($banknotes->id !== $previousBanknotes->id) {
            $admincashTransact->banknotes_id = $banknotes->id;
            if (!$admincashTransact->save()) {
                return false;
            }
            if (!$previousBanknotes->delete()) {
                return false;
            }
        } 
          
        if (!$admincash->add($banknotes)) {   
            return false;
        }
        
        return true;
    }
    
    /**
     * Метод должен вызываться в рамках транзакции.
     * @return boolean
     */
    private static function updateSubOperation($admincashTransact, $banknotes)
    {
        $admincash = $admincashTransact->admincash;
        $previousBanknotes = Banknotes::findOne(['id' => $admincashTransact->banknotes_id]); 
                
        if (!$admincash->add($previousBanknotes)) {  
            return false;
        }
        
        if (!$banknotes->save()) {
            return false;
        }
        
        if ($banknotes->id !== $previousBanknotes->id) {
            $admincashTransact->banknotes_id = $banknotes->id;
            if (!$admincashTransact->save()) {
                return false;
            }
            if (!$previousBanknotes->delete()) {
                return false;
            }
        }
        
        if (!$admincash->sub($banknotes)) {   
            return false;
        }
        
        return true;
    }
    
    private static function updateBanknotes(
        $admincashTransact, 
        $pickercashTransact, 
        $banknotes,
        $previousBanknotes
    ) {        
        if (!$banknotes->save()) {
            return false;
        }
        
        if ($banknotes->id !== $previousBanknotes->id) {
            $admincashTransact->banknotes_id = $banknotes->id;
            $pickercashTransact->banknotes_id = $banknotes->id;
            if (!$previousBanknotes->delete()) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Метод должен вызываться в рамках транзакции.
     * @return boolean
     */
    private static function revertAddOperation($admincashTransact)
    {
        $admincash = $admincashTransact->admincash;
        $banknotes = Banknotes::findOne(['id' => $admincashTransact->banknotes_id]); 
        if (!$admincash->sub($banknotes)) {   
            return false;
        }
        
        return true;
    }
    
    /**
     * Метод должен вызываться в рамках транзакции.
     * @return boolean
     */
    private static function revertSubOperation($admincashTransact)
    {
        $admincash = $admincashTransact->admincash;
        $banknotes = Banknotes::findOne(['id' => $admincashTransact->banknotes_id]);  
        if (!$admincash->add($banknotes)) {   
            return false;
        }
        
        return true;
    }
 
    private static function revertAccdepOperation($admincashTransact)
    {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $admincash = $admincashTransact->admincash;
            $banknotes = Banknotes::findOne(['id' => $admincashTransact->banknotes_id]); 
            
            if ($admincashTransact->isCreated) {
                if (!$admincash->add($banknotes)) {
                    $transaction->rollback();
                    return false;
                }
            } elseif ($admincashTransact->isAccepted) {
                if (!$admincash->add($banknotes)) {
                    $transaction->rollback();
                    return false;
                }
            } elseif ($admincashTransact->isRejected) {
                // Перевод денег был отклонён - ничего делать не нужно.
            }
                       
            if (!$admincashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$banknotes->delete()) {
                $transaction->rollBack();
                return false;  
            }

            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    private static function revertAcctabUserOperation($admincashTransact)
    {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {
            $admincash = $admincashTransact->admincash;
            $banknotes = Banknotes::findOne(['id' => $admincashTransact->banknotes_id]); 
            
            if ($admincashTransact->isCreated) {
                if (!$admincash->add($banknotes)) {
                    $transaction->rollback();
                    return false;
                }
            } elseif ($admincashTransact->isAccepted) {
                // Деньги взяты и возвращены обратно - ничего менять не нужно.
            } elseif ($admincashTransact->isRejected) {
                // В таком состоянии транзакция такого типа быть не должна.
                throw new \LogicException();
            }
                       
            if (!$admincashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$banknotes->delete()) {
                $transaction->rollBack();
                return false;  
            }

            $admincash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    private static function revertAccountableOperation($admincashTransact)
    {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {  
            $accountableTransact = $admincashTransact->accountableTransact;
            $banknotes = $admincashTransact->banknotes;
            
            if ($admincashTransact->isTypeAccountableReplen) {
                if (!self::revertSubOperation($admincashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
                if (!AccountableTransact::revertReplenOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
                
            } elseif ($admincashTransact->isTypeAccountableReturn) {
                if (!self::revertAddOperation($admincashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
                if (!AccountableTransact::revertReturnOperation($accountableTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            } 
         
            if (!$admincashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$accountableTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$banknotes->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash = $admincashTransact->admincash;
            $accountable = $admincash->accountable;
            $admincash->check();
            $accountable->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    private static function revertTransferFromPickercashOperation($admincashTransact)
    {
        $transaction = AdmincashTransact::getDb()->beginTransaction();
        try {  
            $pickercashTransact = $admincashTransact->pickercashTransact;
            $admincash = $admincashTransact->admincash;
            $pickercash = $admincashTransact->pickercash;
            $banknotes = $admincashTransact->banknotes; 

            if ($admincashTransact->isCreated) {
                if (!$pickercash->add($banknotes)) {
                    $transaction->rollBack();
                    return false;
                }  
            } 
            if ($admincashTransact->isAccepted) {        
                if (!$admincash->sub($banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
                if (!$pickercash->add($banknotes)) {
                    $transaction->rollBack();
                    return false;
                }        
            } 
            if ($admincashTransact->isRejected) {
            
        
            }
   
            if (!$admincashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$pickercashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$banknotes->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash->check();
            $pickercash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
                
        return true;
    }
        
    private static function setPickercashTransactState($pickercashTransact, $admincashTransact)
    {
        if ($admincashTransact->isCreated) {
            $pickercashTransact->state = PickercashTransact::STATE_CREATED;
        } elseif ($admincashTransact->isAccepted) {
            $pickercashTransact->state = PickercashTransact::STATE_ACCEPTED;
        } elseif ($admincashTransact->isRejected) {
            $pickercashTransact->state = PickercashTransact::STATE_REJECTED;
        }
    }
         
}
