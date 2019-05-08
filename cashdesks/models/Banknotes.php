<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_banknotes}}".
 *
 * @property integer $id
 * @property integer $count_5000
 * @property integer $count_1000
 * @property integer $count_500
 * @property integer $count_100
 * @property integer $count_50
 * @property integer $rest
 */
class Banknotes extends \yii\db\ActiveRecord
{
    const SCENARIO_EXCHANGE = 'exchange';
    const SCENARIO_POSITIVE = 'positive';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_banknotes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['count_5000', 'integer'],
            ['count_5000', 'default', 'value' => 0],
            ['count_5000', 'integer', 'min' => 0, 'on' => [self::SCENARIO_POSITIVE]],
            
            ['count_1000', 'integer'],
            ['count_1000', 'default', 'value' => 0],
            ['count_1000', 'integer', 'min' => 0, 'on' => [self::SCENARIO_POSITIVE]],
            
            ['count_500', 'integer'],
            ['count_500', 'default', 'value' => 0],
            ['count_500', 'integer', 'min' => 0, 'on' => [self::SCENARIO_POSITIVE]],
            
            ['count_100', 'integer'],
            ['count_100', 'default', 'value' => 0],
            ['count_100', 'integer', 'min' => 0, 'on' => [self::SCENARIO_POSITIVE]],
            
            ['count_50', 'integer'],
            ['count_50', 'default', 'value' => 0],
            ['count_50', 'integer', 'min' => 0, 'on' => [self::SCENARIO_POSITIVE]],
            
            ['rest', 'integer'],
            ['rest', 'default', 'value' => 0],
            ['rest', 'integer', 'min' => 0, 'on' => [self::SCENARIO_POSITIVE]],
            
            ['exchangeSummary', 'validateExchangeSummary', 'on' => self::SCENARIO_EXCHANGE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'count_5000' => 'Купюры по 5000',
            'count_1000' => 'Купюры по 1000',
            'count_500' => 'Купюры по 500',
            'count_100' => 'Купюры по 100',
            'count_50' => 'Купюры по 50',
            'rest' => 'Остаток',
            'sum' => 'Cумма'
        ]; 
    }
    
    // Виртуальное поле нужно только для выполнения валидации в случае размена.
    public function getExchangeSummary()
    {
        return 0;
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
    
    /**
     * @param \app\modules\cashdesks\models\Banknotes $banknotes
     * @return boolean
     */
    public function add($banknotes) 
    {
        return $this->updateCounters([
            'count_5000' => (int)$banknotes->count_5000,
            'count_1000' => (int) $banknotes->count_1000,
            'count_500' => (int) $banknotes->count_500,
            'count_100' => (int) $banknotes->count_100,
            'count_50' => (int) $banknotes->count_50,
            'rest' => (int) $banknotes->rest,
        ]);
    }
    
    /**
     * @param \app\modules\cashdesks\models\Banknotes $banknotes
     * @return  boolean
     */
    public function sub($banknotes)
    {
        return $this->updateCounters([
            'count_5000' => - ((int) $banknotes->count_5000),
            'count_1000' => -((int) $banknotes->count_1000),
            'count_500' => - ((int) $banknotes->count_500),
            'count_100' => -((int) $banknotes->count_100),
            'count_50' => -((int) $banknotes->count_50),
            'rest' => -((int) $banknotes->rest),
        ]);
    }
    
    public function validateExchangeSummary()
    {
        if ($this->sum !== 0) {
            $this->addError('exchangeSummary', 'Общая сумма должна быть равной 0.');
        }
    }
   
}
