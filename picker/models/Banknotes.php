<?php

namespace app\modules\picker\models;

use Yii;

/**
 * This is the model class for table "{{%picker_banknotes}}".
 *
 * @property integer $id
 * @property integer $shifts_courier_id
 * @property integer $count_5000
 * @property integer $count_1000
 * @property integer $count_500
 * @property integer $count_100
 * @property integer $count_50
 * @property integer $rest
 *
 * @property PickerShiftsCourier $shiftsCourier
 */
class Banknotes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picker_banknotes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['shifts_courier_id', 'required'],
            ['shifts_courier_id', 'integer'],
            ['shifts_courier_id', 'unique'],
            [
                'shifts_courier_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => ShiftsCourier::className(), 
                'targetAttribute' => ['shifts_courier_id' => 'id']
            ],
                
            ['count_5000', 'integer'],
            ['count_5000', 'default', 'value' => 0],
            
            ['count_1000', 'integer'],
            ['count_1000', 'default', 'value' => 0],
            
            ['count_500', 'integer'],
            ['count_500', 'default', 'value' => 0],
            
            ['count_100', 'integer'],
            ['count_100', 'default', 'value' => 0],
            
            ['count_50', 'integer'],
            ['count_50', 'default', 'value' => 0],
            
            ['rest', 'integer'],
            ['rest', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shifts_courier_id' => 'Смена курьера',
            'count_5000' => 'Купюры по 5000',
            'count_1000' => 'Купюры по 1000',
            'count_500' => 'Купюры по 500',
            'count_100' => 'Купюры по 100',
            'count_50' => 'Купюры по 50',
            'rest' => 'Остаток',
        ];
    }

    public function getSum() 
    {
        return ($this->count_5000 * 5000) +
               ($this->count_1000 * 1000) +
               ($this->count_500 * 500) +
               ($this->count_100 * 100) + 
               ($this->count_50 * 50) +
               ($this->rest);
    }
    
    public function getAbsSum()
    {
        return abs($this->count_5000 * 5000) +
               abs($this->count_1000 * 1000) +
               abs($this->count_500 * 500) +
               abs($this->count_100 * 100) + 
               abs($this->count_50 * 50) +
               abs($this->rest);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShiftsCourier()
    {
        return $this->hasOne(ShiftsCourier::className(), ['id' => 'shifts_courier_id']);
    }
    
}
