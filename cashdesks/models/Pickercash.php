<?php

namespace app\modules\cashdesks\models;

use Yii;

use app\modules\cashdesks\exceptions\PickercashNotEnoughMoneyException;

/**
 * This is the model class for table "{{%cashdesks_pickercash}}".
 *
 * @property integer $id
 * @property integer $depart_id
 * @property integer $banknotes_id
 *
 * @property Banknotes $banknotes
 */
class Pickercash extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_pickercash}}';
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
    
    public function getAdmincash() 
    {
        return $this->hasOne(Admincash::className(), ['depart_id' => 'depart_id']);
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
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanknotes()
    {
        return $this->hasOne(Banknotes::className(), ['id' => 'banknotes_id']);
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
            throw new PickercashNotEnoughMoneyException('Для выполнения  недостаточно денег в кассе комплектовщика.');
        }
    }
    
}
