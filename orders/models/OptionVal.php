<?php

namespace app\modules\orders\models;

use Yii;

/**
 * This is the model class for table "{{%orders_option_vals}}".
 *
 * @property string $option_id
 * @property string $value
 * @property integer $city_id
 *
 * @property Option $option
 */
class OptionVal extends \yii\db\ActiveRecord
{
    private static $valuesCache;
    
    const TIME_VALUES_PATTERN = '/^([0-9]{2}):([0-9]{2})$/i';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_option_vals}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['option_id', 'required'],
            ['option_id', 'string', 'max' => 255],
            [
                'option_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Option::className(), 
                'targetAttribute' => ['option_id' => 'id']
            ],
            
            ['value', 'required'],
            ['value', 'string', 'max' => 255],
            
            ['city_id', 'required'],
            ['city_id', 'integer'],
            ['city_id', 'unique', 'targetAttribute' => ['city_id', 'option_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'option_id' => 'Опция',
            'value' => 'Значение',
            'city_id' => 'Город',
            'optionName' => 'Опция',
        ];
    }
    
    public function getOptionName()
    {
        return $this->option->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Option::className(), ['id' => 'option_id']);
    }
    
    public static function getValueByCityId($optionId, $cityId)
    {
        $optionVal = static::findOne([
            'option_id' => $optionId,
            'city_id' => $cityId
        ]);
        
        if ($optionVal !== null) {
            return $optionVal->value;
        }
        
        $defaultCiytId = OrdersApi::getDefaultCityId();

        $defaultOptionVal = static::findOne([
            'option_id' => $optionId,
            'city_id' => $defaultCiytId 
        ]);
                
        if ($defaultOptionVal !== null) {
            return $defaultOptionVal->value;
        }
        
        return null;
    }
        
    public static function getMinPossibleDeliveryTimeValue($cityId)
    {
        return static::getCachedValue('min_possible_dilivery_time', $cityId);
    }
    
    public static function getDeliveryCostValue($cityId)
    {
        return static::getCachedValue('delivery_cost', $cityId);
    }
    
    public static function getDeliveryTimeValue($cityId)
    {
        return static::getCachedValue('delivery_time', $cityId);
    }
    
    public static function getMinTotalPriceForFreeDelivery($cityId)
    {
        return static::getCachedValue('min_total_price_for_free_delivery', $cityId);
    }
    
    public static function getExpiryTimeValue($cityId)
    {
        return static::getCachedValue('expiry_time', $cityId);
    }
    
    public static function getStationOrdersCount($cityId)
    {
        return static::getCachedValue('station_orders_count', $cityId);
    }
    
    public static function getSecretKeyDefault()
    {
        return static::getCachedValue('secret_key', OrdersApi::getDefaultCityId());
    }
    
    /**
     * Используется для генериции кнопок при вводе времени доставки.
     * @param integer $cityId
     * @return string
     */
    public static function getMaxPossibleDeliveryTimeValue($cityId)
    {
        return '23:59';
    }
    
    
    public static function getMinPossibleDeliveryTimeHoursValue($cityId)
    {
        $value = static::getMinPossibleDeliveryTimeValue($cityId);
        if ($value === null) {
            return null;
        }
        
        return self::getHoursFromTimeStr($value);
    }
    
    public static function getMaxPossibleDeliveryTimeHoursValue($cityId)
    {
        $value = static::getMaxPossibleDeliveryTimeValue($cityId);
        if ($value === null) {
            return null;
        }
        
        return self::getHoursFromTimeStr($value);
    }
    
    private static function getHoursFromTimeStr($timeStr)
    {   
        $matches = [];

        if (!preg_match(self::TIME_VALUES_PATTERN, $timeStr, $matches)) {
            return null;
        } 
        $hoursStr = $matches[1];
        if ($hoursStr[0] === '0') {
            return (int) $hoursStr[1];
        } else {
            return (int) $hoursStr;
        }
    }
    
    private static function getCachedValue($optionId, $cityId)
    {
        $key = $cityId.'-'.$optionId;
        if (!isset(self::$valuesCache[$key])) {
            self::$valuesCache[$key] = self::getValueByCityId($optionId, $cityId);
        }
                
        return self::$valuesCache[$key];
    }
     
}
