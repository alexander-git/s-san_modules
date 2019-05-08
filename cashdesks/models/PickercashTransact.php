<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_pickercash_transact}}".
 *
 * @property integer $id
 * @property integer $depart_id
 * @property integer $type
 * @property integer $banknotes_id
 * @property integer $date_create
 * @property integer $date_end
 * @property integer $picker_id
 * @property integer $user_id
 * @property integer $state
 * @property string $desc
 *
 * @property CashdesksBanknotes $banknotes
 */
class PickercashTransact extends \yii\db\ActiveRecord
{
    const STATE_CREATED = 0;
    const STATE_ACCEPTED = 1;
    const STATE_REJECTED = 2;
    
    const TYPE_REPLEN = 0;
    const TYPE_TRANSFER_TO_ADMINCASH = 1;
    const TYPE_EXCHANGE = 2;
   
    const SCENARIO_REPLEN_CREATE = 'replenCreate';
    const SCENARIO_REPLEN_UPDATE = 'replenUpdate';
    
    const SCENARIO_CHANGE_PICKERCASH_CREATE = 'changePickercashCreate';
    const SCENARIO_CHANGE_PICKERCASH_UPDATE = 'changePickercashUpdate';
    
    const SCENARIO_TRANSFER_TO_ADMINCASH_CREATE = 'transferToAdmincashCreate';
    const SCENARIO_TRANSFER_TO_ADMINCASH_ACCEPT = 'transferToAdmincashAccept';
    const SCENARIO_TRANSFER_TO_ADMINCASH_REJECT = 'transferToAdmincashReject';
    const SCENARIO_TRANSFER_TO_ADMINCASH_UPDATE = 'transferToAdmincashUpdate';
    const SCENARIO_TRANSFER_TO_ADMINCASH_UPDATE_PICKER = 'transferToAdmincashUpdatePicker';

    const TYPE_NAME_REPLEN_COURIER = 'Пополнение (курьер)';
    const TYPE_NAME_REPLEN_PICKUP = 'Пополнение (самовывоз)';
        
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_pickercash_transact}}';
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
            
            ['picker_id', 'required'],
            ['picker_id', 'integer'],

            ['date_create', 'required'],
            ['date_create', 'integer'],
            
            ['date_end', 'integer'],
            ['user_id', 'required', 'on' => [
                self::SCENARIO_REPLEN_CREATE,
                self::SCENARIO_REPLEN_UPDATE,
                self::SCENARIO_TRANSFER_TO_ADMINCASH_ACCEPT,
                self::SCENARIO_TRANSFER_TO_ADMINCASH_REJECT,
            ]],
            ['user_id', 'integer'],

            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
            
            ['desc', 'required', 'on' => [
                self::SCENARIO_REPLEN_CREATE,
                self::SCENARIO_REPLEN_UPDATE,
                self::SCENARIO_CHANGE_PICKERCASH_CREATE,
                self::SCENARIO_CHANGE_PICKERCASH_UPDATE,
                self::SCENARIO_TRANSFER_TO_ADMINCASH_ACCEPT,
                self::SCENARIO_TRANSFER_TO_ADMINCASH_REJECT,
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
            'date_create' => 'Дата создания',
            'date_end' => 'Дата завершения',
            'picker_id' => 'Комплектовщик',
            'user_id' => 'Пользователь',
            'state' => 'Состояние',
            'desc' => 'Описание',
        ];
    }
            
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_REPLEN_CREATE] = [
            'user_id',
            'desc',
            '!depart_id',  
            '!banknotes_id',
            '!type',
            '!state',
            '!picker_id',
            '!date_create',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_REPLEN_UPDATE] = [
            'user_id',
            'desc',
            '!banknotes_id',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_CHANGE_PICKERCASH_CREATE] = [
            'desc',
            '!depart_id',  
            '!banknotes_id',
            '!type',
            '!state',
            '!picker_id',
            '!user_id',
            '!date_create',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_CHANGE_PICKERCASH_UPDATE] = [
            'desc',
            '!banknotes_id',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_TRANSFER_TO_ADMINCASH_CREATE] = [
            'desc',
            '!depart_id',  
            '!banknotes_id',
            '!type',
            '!state',
            '!picker_id',
            '!user_id',
            '!date_create',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_TRANSFER_TO_ADMINCASH_UPDATE_PICKER] = [
            'desc',    
            '!banknotes_id',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_TRANSFER_TO_ADMINCASH_UPDATE] = [
            'desc',    
            'state',
            '!banknotes_id',
            '!date_end',
        ];
        
        $scenarios[self::SCENARIO_TRANSFER_TO_ADMINCASH_ACCEPT] = [
            'desc',
            '!state',
            '!user_id',
            '!date_end',
        ];

        $scenarios[self::SCENARIO_TRANSFER_TO_ADMINCASH_REJECT] = [
            'desc',
            '!state',
            '!user_id',
            '!date_end',
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
    
    public function getStateName()
    {
        return static::getStatesArray()[$this->state];
    }
    
    public function getIsCreated()
    {
        return $this->state === self::STATE_CREATED;
    }
    
    public function getIsAccepted()
    {
        return $this->state === self::STATE_ACCEPTED;
    }
    
    public function getIsRejected()
    {
        return $this->state === self::STATE_REJECTED;
    }
    
    public static function getTypesArray()
    {
        return [
            self::TYPE_REPLEN => 'Пополнение',
            self::TYPE_TRANSFER_TO_ADMINCASH => 'Перевод администратору',
            self::TYPE_EXCHANGE => 'Размен',
        ];
    }
    
    public function getTypeName()
    {
        return static::getTypesArray()[$this->type];
    }
    
    public function getTypeNameAdvanced()
    {
        if ($this->isReplenCourier) {
            return self::TYPE_NAME_REPLEN_COURIER;
        } elseif ($this->isReplenPickup) {
            return self::TYPE_NAME_REPLEN_PICKUP;
        } else {
            return $this->getTypeName();
        }
    }
    
    public function getIsTypeReplen()
    {
        return $this->type === self::TYPE_REPLEN;
    }
    
    public function getIsTypeTransferToAdmincash()
    {
        return $this->type === self::TYPE_TRANSFER_TO_ADMINCASH;
    }
    
    public function getIsTypeExchange()
    {
        return $this->type === self::TYPE_EXCHANGE;
    }
    
    /**
     * Вспомогательная функция для того, чтобы отличать пополнение от курьера и
     * пополнения от самовывоза.
     */ 
    public function getIsReplenCourier()
    {
        return $this->isTypeReplen && ($this->user_id !== $this->picker_id);
    }
    
    /**
     * Вспомогательная функция для того, чтобы отличать пополнение от курьера и
     * пополнения от самовывоза.
     */ 
    public function getIsReplenPickup()
    {
        return $this->isTypeReplen && ($this->user_id === $this->picker_id);
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
        return CashdesksApi::getUserName($this->picker_id);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanknotes()
    {
        return $this->hasOne(Banknotes::className(), ['id' => 'banknotes_id']);
    }
    
    
    public function getPickercash() 
    {
        return $this->hasOne(Pickercash::className(), ['depart_id' => 'depart_id']);
    }   
    
    
    public function getAdmincash() 
    {
        return $this->hasOne(Admincash::className(), ['depart_id' => 'depart_id']);
    }
    
    public function getAdmincashTransact()
    {
        if (!$this->isTypeTransferToAdmincash) {
            return null;
        }
        
        return AdmincashTransact::find()
            ->where([
                'type_id' => $this->id,
                'type' => AdmincashTransact::TYPE_TRANSFER_FROM_PICKERCASH,
            ]);
    }
    
    
    public function setUpdateScenario($isUpdateByPicker = false)
    {
        if ($this->isTypeReplen) {
           $this->scenario = self::SCENARIO_REPLEN_UPDATE;
        }
        
        if ($this->isTypeTransferToAdmincash) {
            // Комплектовщик не может менять состоянии транзакции.
            if ($isUpdateByPicker) { 
                $this->scenario = self::SCENARIO_TRANSFER_TO_ADMINCASH_UPDATE_PICKER;
                return;
            } else {
                $this->scenario = self::SCENARIO_TRANSFER_TO_ADMINCASH_UPDATE;
                return;
            } 
        } 
        
        if ($this->isTypeExchange) {
            $this->scenario = self::SCENARIO_CHANGE_PICKERCASH_UPDATE;
            return;
        }
    }
    
    public static function createReplenCourier(
        $pickercashTransact, 
        $pickerId,
        $pickercash,
        $banknotes
    ) {
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $pickercashTransact->depart_id = $pickercash->depart_id;
            $pickercashTransact->type = self::TYPE_REPLEN;
            $pickercashTransact->state = self::STATE_ACCEPTED;
            $pickercashTransact->picker_id = $pickerId;
            $pickercashTransact->date_create = $time;
            $pickercashTransact->date_end = $time;
            $pickercashTransact->banknotes_id = $banknotes->id;
            
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
          
            if (!$pickercash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $pickercash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function createReplenPickup(
        $pickercashTransact, 
        $pickerId,
        $pickercash,
        $banknotes
    ) {
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $pickercashTransact->depart_id = $pickercash->depart_id;
            $pickercashTransact->type = self::TYPE_REPLEN;
            $pickercashTransact->state = self::STATE_ACCEPTED;
            $pickercashTransact->picker_id = $pickerId;
            $pickercashTransact->user_id = $pickerId;
            $pickercashTransact->date_create = $time;
            $pickercashTransact->date_end = $time;
            $pickercashTransact->banknotes_id = $banknotes->id;
            
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
          
            if (!$pickercash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $pickercash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function exchangePickercash(
        $pickercashTransact, 
        $pickerId,
        $pickercash,
        $banknotes
    ) {
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()){
                $transaction->rollBack();
                return false;
            }
            
            $pickercashTransact->depart_id = $pickercash->depart_id;
            $pickercashTransact->type = self::TYPE_EXCHANGE;
            $pickercashTransact->state = self::STATE_ACCEPTED;
            $pickercashTransact->picker_id = $pickerId;
            $pickercashTransact->user_id = null;
            $pickercashTransact->date_create = $time;
            $pickercashTransact->date_end = $time;
            
            $pickercashTransact->banknotes_id = $banknotes->id;
            
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
          
            if (!$pickercash->add($banknotes)) {
                $transaction->rollBack();   
                return false;
            }
            
            $pickercash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function createTransferToAdmincash(
        $pickercashTransact, 
        $pickerId,
        $pickercash,
        $banknotes
    ) {
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            if (!$banknotes->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $pickercashTransact->depart_id = $pickercash->depart_id;
            $pickercashTransact->type = self::TYPE_TRANSFER_TO_ADMINCASH;
            $pickercashTransact->state = self::STATE_CREATED;
            $pickercashTransact->picker_id = $pickerId;
            $pickercashTransact->user_id = null;
            $pickercashTransact->date_create = $time;    
            $pickercashTransact->banknotes_id = $banknotes->id;
            
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $createAdmincashTransactSuccess = AdmincashTransact::createTransferFromPickercash(
                $pickercashTransact, 
                $pickerId, 
                $banknotes, 
                $time
            ); 
            
            if (!$createAdmincashTransactSuccess) {
                $transaction->rollBack();
                return false;
            }
           
            if (!$pickercash->sub($banknotes)) {
                $transaction->rollBack();
                return false;
            }
            
            $admincash = $pickercash->admincash;
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
    
    
    public static function acceptTransferToAdmincash(
        $pickercashTransact, 
        $administratorId
    ) {
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
           
            $pickercashTransact->state = self::STATE_ACCEPTED;
            $pickercashTransact->user_id = $administratorId;
            $pickercashTransact->date_end = $time;    
           
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
              
            $admincashTransact = $pickercashTransact->admincashTransact;
            $banknotes = $pickercashTransact->banknotes;
            $admincash = $pickercashTransact->admincash;
            $pickercash =  $pickercashTransact->pickercash;
        
            $admincashTransact->state = AdmincashTransact::STATE_ACCEPTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            
            if (!empty($pickercashTransact->desc)) {
                $admincashTransact->desc = $pickercashTransact->desc;
            }
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }

            if (!$admincash->add($banknotes)) {
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
    
    public static function rejectTransferToAdmincash(
        $pickercashTransact, 
        $administratorId
    ) {
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
            
            $admincashTransact = $pickercashTransact->admincashTransact;
            $admincash = $admincashTransact->admincash;
            $pickercash = $admincash->pickercash;
            
            $pickercashTransact->state = self::STATE_REJECTED;
            $pickercashTransact->user_id = $administratorId;
            $pickercashTransact->date_end = $time;    
           
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $admincashTransact->state = AdmincashTransact::STATE_REJECTED;
            $admincashTransact->administrator_id = $administratorId;
            $admincashTransact->date_end = $time;
            $admincashTransact->date_edit = $time;
            $admincashTransact->user_edit_id = $administratorId;
            
            if (!empty($pickercashTransact->desc)) {
                $admincashTransact->desc = $pickercashTransact->desc;
            }
            
            if (!$admincashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$pickercash->add($pickercashTransact->banknotes)) {
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
 
     
    public static function updateOperation(
        $pickercashTransact, 
        $userEditId,
        $banknotes = null
    ) {
        
        if ($pickercashTransact->isTypeTransferToAdmincash) {
            return self::updateTransferToAdmincashOperation(
                $pickercashTransact, 
                $userEditId, 
                $banknotes
            );        
        }
        
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
                       
            if ($pickercashTransact->isTypeReplen) {
                if (!self::updateAddOperation($pickercashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
                if ($pickercashTransact->isReplenPickup) {
                    $pickercashTransact->user_id = $pickercashTransact->picker_id;
                }
            }
            
            if ($pickercashTransact->isTypeExchange) {
                if (!self::updateAddOperation($pickercashTransact, $banknotes)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if ($pickercashTransact->isTypeTransferToAdmincash) {
                throw new \LogicException();
            }
    
            $pickercashTransact->date_end = $time;    
           
            if (!$pickercashTransact->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $pickercash = $pickercashTransact->pickercash;
            $pickercash->check();
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    
    public static function revertOperation($pickercashTransact) 
    {
        if ($pickercashTransact->isTypeTransferToAdmincash) {
            $admincashTransact = $pickercashTransact->admincashTransact;
            return AdmincashTransact::revertOperation($admincashTransact);        
        }
        
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            if ($pickercashTransact->isTypeReplen) {
                if (!self::revertAddOperation($pickercashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if ($pickercashTransact->isTypeExchange) {
                if (!self::revertAddOperation($pickercashTransact)) {
                    $transaction->rollBack();
                    return false;
                }
            }

            $banknotes = $pickercashTransact->banknotes;
            
            if (!$pickercashTransact->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$banknotes->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $pickercash = $pickercashTransact->pickercash;
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
    private static function updateAddOperation(
        $pickercashTransact, 
        $banknotes
    ) {
        $previousBanknotes = Banknotes::findOne(['id' => $pickercashTransact->banknotes_id]);  
        $pickercash = $pickercashTransact->pickercash;
        
        if (!$pickercash->sub($previousBanknotes)) {  
            return false;
        }
        
        if (!$banknotes->save()) {
            return false;
        }  
                
        if ($banknotes->id !== $previousBanknotes->id) {
            $pickercashTransact->banknotes_id = $banknotes->id;
            if (!$pickercashTransact->save()) {
                return false;
            }
            if (!$previousBanknotes->delete()) {
                return false;
            }
        } 
          
        if (!$pickercash->add($banknotes)) {   
            return false;
        }
        
        return true;
    }
    
    /**
     * Метод должен вызываться в рамках транзакции.
     * @return boolean
     */
    private static function updateSubOperation(
        $pickercashTransact,  
        $banknotes
    ) {
        $previousBanknotes = Banknotes::findOne(['id' => $pickercashTransact->banknotes_id]); 
        $pickercash = $pickercashTransact->pickercash;
        
        if (!$pickercash->add($previousBanknotes)) {  
            return false;
        }
        
        if (!$banknotes->save()) {
            return false;
        }
        
        if ($banknotes->id !== $previousBanknotes->id) {
            $pickercashTransact->banknotes_id = $banknotes->id;
            if (!$pickercashTransact->save()) {
                return false;
            }
            if (!$previousBanknotes->delete()) {
                return false;
            }
        }
        
        if (!$pickercash->sub($banknotes)) {   
            return false;
        }
        
        return true;
    }
        
    public static function updateTransferToAdmincashOperation(
        $pickercashTransact,
        $userEditId,
        $banknotes
    ) {
        if (!$pickercashTransact->isCreated) {
            throw new \LogicException();
        }
        
        $transaction = PickercashTransact::getDb()->beginTransaction();
        try {
            $time  = CashdesksApi::getCurrentTimestamp();
                       
            $admincashTransact = $pickercashTransact->admincashTransact;
            $pickercash = $pickercashTransact->pickercash;
            $admincash = $pickercash->admincash;
            
            $previousBanknotes = Banknotes::findOne(['id' => $pickercashTransact->banknotes_id]);
            $admincashTransact->scenario = AdmincashTransact::SCENARIO_TRNASFER_FROM_PICKERCASH_UPDATE_PICKER;
    
            $admincashTransact->user_edit_id = $userEditId;
            $admincashTransact->date_edit = $time;
            
            //$pickercashTransact->date_end = $time;  
            
            if (!empty($pickercashTransact->desc)) {
                $admincashTransact->desc = $pickercashTransact->desc;
            }
                        
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
    private static function revertAddOperation($pickercashTransact)
    {
        $pickercash = $pickercashTransact->pickercash;
        $banknotes = Banknotes::findOne(['id' => $pickercashTransact->banknotes_id]); 
        if (!$pickercash->sub($banknotes)) {   
            return false;
        }
        
        return true;
    }
    
    /**
     * Метод должен вызываться в рамках транзакции.
     * @return boolean
     */
    private static function revertSubOperation($pickercashTransact)
    {
        $pickercash = $pickercashTransact->pickercash;
        $banknotes = Banknotes::findOne(['id' => $pickercashTransact->banknotes_id]);  
        if (!$pickercash->add($banknotes)) {   
            return false;
        }
        
        return true;
    }
    
    
}
