<?php

namespace app\modules\clients\models;

use Yii;

/**
 * This is the model class for table "clients_bonuscard".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $moneyquan
 * @property integer $bonuses
 *
 * @property ClientsBonuscardType $type0
 * @property ClientsClients[] $clientsClients
 */
class Bonuscard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%clients_bonuscard}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'required'],
            ['type', 'integer'],
            [
                'type', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => BonuscardType::className(), 
                'targetAttribute' => ['type' => 'id']
            ],
 
            ['moneyquan', 'required'],
            ['moneyquan', 'integer'],
            ['moneyquan', 'validateMoneyquan'],
            
            ['bonuses', 'required'],
            ['bonuses', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'moneyquan' => 'Потраченные деньги',
            'bonuses' => 'Бонусов на счету',
        ];
    }
    
    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
        ];
    }
    
    public function beforeDelete()
    {
        $this->client->cardnum = null;
        if (!$this->client->save()) {
            throw new \Exception();
        }
        
        return parent::beforeDelete();
    } 

    public function validateMoneyquan()
    {
        if ($this->hasErrors()) {
            return;
        }
        $bonuscardType = BonuscardType::findOne(['id' => $this->type]);
        if ((int) $this->moneyquan < $bonuscardType->minmoney) {
            $this->addError('moneyquan', 'Для бонусной карты этого типа недостаточно потраченных денег');
        }
    }
    

    public static function createBonuscard($clientModel, $bonuscardModel)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            if (!$bonuscardModel->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $clientModel->cardnum = $bonuscardModel->id;
            if (!$clientModel->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw  $e;
        }    
    }
    
    /*
    public static function deleteBonuscard($bonuscardModel)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $clientModel = $bonuscardModel->client;
            
            $clientModel->cardnum = null;
            if (!$clientModel->save()) {
                $transaction->rollBack();
                return false;
            }
        
            if (!$bonuscardModel->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw  $e;
        }    
    }
    */
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonuscardType()
    {
        return $this->hasOne(BonuscardType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['cardnum' => 'id']);
    }
    
}
