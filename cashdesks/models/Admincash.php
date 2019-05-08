<?php

namespace app\modules\cashdesks\models;

use Yii;

use app\modules\cashdesks\exceptions\AdmincashNotEnoughMoneyException;

/**
 * This is the model class for table "{{%cashdesks_admincash}}".
 *
 * @property integer $id
 * @property integer $depart_id
 * @property integer $banknotes_id
 *
 * @property Banknotes $banknotes
 */
class Admincash extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_admincash}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            ['depart_id', 'unique'],
            
            ['banknotes_id', 'required'],
            ['banknotes_id', 'integer'],
            [
                'banknotes_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Banknotes::className(),
                'targetAttribute' => ['banknotes_id' => 'id'],
            ],
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
            'banknotes_id' => 'Купюры',
        ];
    }
    
    public function getDepartmentName()
    {
        return CashdesksApi::getDepartmentName($this->depart_id);
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
    
    public function getAccountable()
    {
        return $this->hasOne(Accountable::className(), ['depart_id' => 'depart_id']);
    }
    
    /**
     * @param \app\modules\cashdesks\models\Banknotes $banknotes
     * @return boolean
     */
    public function add($banknotes) 
    {
        return $this->banknotes->add($banknotes);
    }
    
    /**
     * @param \app\modules\cashdesks\models\Banknotes $banknotes
     * @return  boolean
     */
    public function sub($banknotes) 
    {
        return $this->banknotes->sub($banknotes);
    }
    
    public static function createCashdesksForDepartment($departmentId)
    {
        $transaction = Admincash::getDb()->beginTransaction();
        try {
            $admincashBanknotes = new Banknotes();
            if (!$admincashBanknotes->save()) {
                throw new \Exception();
            }
            
            $admincash = new Admincash();
            $admincash->depart_id = $departmentId;
            $admincash->banknotes_id = $admincashBanknotes->id;
            if (!$admincash->save()) {
                throw new \Exception();
            }
            
            $pickercashBanknotes = new Banknotes();
            if (!$pickercashBanknotes->save()) {
                throw new \Exception();
            }
            
            $pickercash = new Pickercash();
            $pickercash->depart_id = $departmentId;
            $pickercash->banknotes_id = $pickercashBanknotes->id;
            if (!$pickercash->save()) {
                throw new \Exception();
            }
            
            $accountable = new Accountable();
            $accountable->depart_id = $departmentId;
            if (!$accountable->save()) {
                throw new \Exception();
            }
            
            $transaction->commit();
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public function check()
    {
        $banknotesId = $this->banknotes_id;
        $banknotes = Banknotes::findOne(['id' => $banknotesId]);
        if (
            $banknotes->count_5000 < 0 ||
            $banknotes->count_1000 < 0 ||
            $banknotes->count_500 < 0 ||
            $banknotes->count_100 < 0 ||
            $banknotes->count_50 < 0 ||
            $banknotes->rest < 0
        ) {
            throw new AdmincashNotEnoughMoneyException('Для выполнения недостаточно денег в сейфе администратора.');
        }
    }
      
}
