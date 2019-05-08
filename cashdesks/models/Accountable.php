<?php

namespace app\modules\cashdesks\models;

use Yii;

use app\modules\cashdesks\exceptions\AccountableNotEnoughMoneyException;

/**
 * This is the model class for table "{{%cashdesks_accountable}}".
 *
 * @property integer $depart_id
 * @property integer $current
 * @property integer $max_sum
 *
 * @property AccountableTransact[] $cashdesksAccountableTransacts
 */
class Accountable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_accountable}}';
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
            
            ['current', 'integer'],
            ['current', 'default', 'value' => 0],
            
            ['max_sum', 'integer'],
            ['max_sum', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'depart_id' => 'Департамент',
            'current' => 'Текщая сумма',
            'max_sum' => 'Максимальная сумма',
        ];
    }
    
    public function getDepartmentName()
    {
        return CashdesksApi::getDepartmentName($this->depart_id);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountableTransacts()
    {
        return $this->hasMany(AccountableTransact::className(), ['depart_id' => 'depart_id']);
    }
    
    public function addCurrent($value) 
    {
        return $this->updateCounters(['current' => (int) $value]);
    }
    
    public function subCurrent($value) 
    {
        return $this->updateCounters(['current' => (- (int) $value)]); 
    }
    
    public function addMaxSum($value) 
    {
        return $this->updateCounters(['max_sum' => (int) $value]);
    }
    
    public function subMaxSum($value) 
    {
        return $this->updateCounters(['max_sum' => (- (int) $value)]); 
    }
    
    public function check()
    {
        $accountable = Accountable::findOne(['depart_id' => $this->depart_id]);
        if (
            $accountable->current < 0 ||
            $accountable->max_sum < 0 
        ) {
            throw new AccountableNotEnoughMoneyException('Для выполнения недостаточно денег в кассе "под отчёт".');
        }
    }
    
}
