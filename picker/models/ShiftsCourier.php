<?php

namespace app\modules\picker\models;

use Yii;

/**
 * This is the model class for table "{{%picker_shifts_courier}}".
 *
 * @property integer $id
 * @property integer $date_open
 * @property integer $date_close
 * @property integer $shifts_id
 * @property integer $shifts_picker_id
 * @property integer $courier_id
 * @property string $courier_name
 * @property string $courier_phone
 * @property integer $type_courier
 * @property integer $check_sum
 * @property integer $check_nocash
 * @property integer $count_order
 * @property integer $count_trip
 * @property integer $spend
 * @property integer $gifts
 * @property integer $payment
 * @property integer $cash
 * @property string $message
 * @property integer $state
 *
 * @property PickerShifts $shifts
 * @property PickerShiftsPicker $shiftsPicker
 */
class ShiftsCourier extends \yii\db\ActiveRecord
{
    const SCENARIO_OPEN_DEFAULT = 'openDefault';
    const SCENARIO_OPEN_ADDITIONAL = 'openAdditional';
    const SCENARIO_OPEN_PICKUP = 'openPickup';
    const SCENARIO_UPDATE_DEFAULT = 'updateDefault';
    const SCENARIO_UPDATE_ADDITIONAL = 'updateAdditional';
    const SCENARIO_FILL_DEFAULT = 'fillDefault';
    const SCENARIO_FILL_ADDITIONAL = 'fillAdditional';
    const SCENARIO_FILL_PICKUP = 'fillPickup';
    
    const STATE_OPENED = 0;
    const STATE_CLOSED = 1;
    
    const TYPE_COURIER_PICKUP = 0;
    const TYPE_COURIER_DAY = 1;
    const TYPE_COURIER_EVENING = 2;
    const TYPE_COURIER_ADDITIONAL = 3;
    
    const PICKUP_COUIRER_NAME = 'Самовывоз';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picker_shifts_courier}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['date_open', 'required'],
            ['date_open', 'integer'],
            
            ['date_close', 'integer'],
            
            ['shifts_id', 'required'],
            ['shifts_id', 'integer'],
            [
                'shifts_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Shifts::className(), 
                'targetAttribute' => ['shifts_id' => 'id']
            ],
            
            ['shifts_picker_id', 'required'],
            ['shifts_picker_id', 'integer'],
            [
                'shifts_picker_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => ShiftsPicker::className(), 
                'targetAttribute' => ['shifts_picker_id' => 'id']
            ],
            
            ['courier_id', 'integer'],
            
            ['courier_name', 'required'],
            ['courier_name', 'string', 'max' => 255],
            
            [
                'courier_phone', 
                'required', 
                'on' => [
                    self::SCENARIO_OPEN_DEFAULT,
                    self::SCENARIO_OPEN_ADDITIONAL,
                    self::SCENARIO_UPDATE_DEFAULT,
                    self::SCENARIO_UPDATE_ADDITIONAL,
                ],
            ],
            ['courier_phone', 'string', 'max' => 255],
            
            ['type_courier', 'required'],
            [
                'type_courier', 
                'in', 
                'range' => array_keys(self::getTypeCouriersArray())
            ],
            [
                'type_courier', 
                'in', 
                'range' => array_keys(self::getTypeCouriersArrayDefault()),
                'on' => [self::SCENARIO_OPEN_DEFAULT, self::SCENARIO_FILL_DEFAULT],
            ],
            
            
            ['check_sum', 'integer'],
            ['check_sum', 'default', 'value' => 0],
            
            ['check_nocash', 'integer'],
            ['check_nocash', 'default', 'value' => 0],
            
            ['count_order', 'integer'],
            ['count_order', 'default', 'value' => 0],
            
            ['count_trip', 'integer'],
            ['count_trip', 'default', 'value' => 0],
            
            ['spend', 'integer'],
            ['spend', 'default', 'value' => 0],
            
            ['gifts', 'integer'],
            ['gifts', 'default', 'value' => 0],
            
            ['payment', 'integer'],
            
            ['cash', 'integer'],
            
            ['message', 'string', 'max' => 255],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
        ];
    }
    
   
    public function scenarios() 
    {
        $scenarios = parent::scenarios();
        
         $scenarios[self::SCENARIO_OPEN_DEFAULT] = [
            'courier_id',
            'type_courier',
             
            '!date_open',
            '!shifts_id',
            '!shifts_picker_id',
            '!courier_name',
            '!courier_phone',
            '!state',
        ];
        $scenarios[self::SCENARIO_OPEN_ADDITIONAL] = [
            'courier_name',
            'courier_phone',
            
            '!date_open',
            '!shifts_id',
            '!shifts_picker_id',
            '!courier_id',
            '!type_courier',
            '!state',
        ];
        
        
        $scenarios[self::SCENARIO_OPEN_PICKUP] = [
            '!date_open',
            '!shifts_id',
            '!shifts_picker_id',
            '!courier_id',
            '!courier_name',
            '!courier_phone',
            '!type_courier',
            '!state',
        ];
        
        $scenarios[self::SCENARIO_UPDATE_DEFAULT] = [
            'courier_id',
            'type_courier',
            '!courier_name',
            '!courier_phone'
        ];
        
        $scenarios[self::SCENARIO_UPDATE_ADDITIONAL] = [
            'courier_name',
            'courier_phone',
            '!courier_id',
        ];
        
        
        $scenarios[self::SCENARIO_FILL_DEFAULT] = [
            'type_courier',
            'check_sum',
            'check_nocash',
            'count_order',
            'count_trip',
            'spend',
            'gifts',
            'message',
        ];
        
        $scenarios[self::SCENARIO_FILL_ADDITIONAL] = [
            'check_sum',
            'check_nocash',
            'count_order',
            'count_trip',
            'gifts',
            'message',
        ];
        
        $scenarios[self::SCENARIO_FILL_PICKUP] = [
            'check_sum',
            'check_nocash',
            'count_order',
            'gifts',
            'message',
        ];
         
        return $scenarios;
    }
 
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_open' => 'Открыта',
            'date_close' => 'Закрыта',
            'shifts_id' => 'Суточная смена',
            'shifts_picker_id' => 'Смена комплектовщика',
            'courier_id' => 'Курьер',
            'courier_name' => 'Имя курьера',
            'courier_phone' => 'Телефон курьера',
            'type_courier' => 'Тип курьера',
            'check_sum' => 'Сумма заказов по чеку',
            'check_nocash' => 'Сумма заказов по безналу',
            'count_order' => 'Кол-во выполненных заказов',
            'count_trip' => 'Кол-во поездок',
            'spend' => 'Потрачено на доп.покупки',
            'gifts' => 'Подарки/Сертефикаты',
            'payment' => 'Зарплата за день',
            'cash' => 'Сдал наличности',
            'message' => 'Комментарий',
            'state' => 'Состояние',
            
            'debt' => 'Долг',
            'cashRequired' => 'Должен сдать наличности',
            'cashBalance' => 'Не хватает',
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
        $banknotes = $this->getBanknotes()->all();
        foreach($banknotes as $banknote) {
            $banknote->delete();
        }
        return parent::beforeDelete();
    }
    
    public static function getStatesArray()
    {
        return [
            self::STATE_OPENED => 'Открыта',
            self::STATE_CLOSED => 'Закрыта',
        ];
    }
    
    public function getStateName() 
    {
        return static::getStatesArray()[$this->state];
    }
    
    public static function getTypeCouriersArray()
    {
        return [
            self::TYPE_COURIER_PICKUP => 'Самовывоз',
            self::TYPE_COURIER_DAY => 'Дневной',    
            self::TYPE_COURIER_EVENING => 'Вечерний',
            self::TYPE_COURIER_ADDITIONAL => 'Дополнительный',
        ];
    }
    
    public function getTypeCourierName()
    {
        return static::getTypeCouriersArray()[$this->type_courier];
    }
    
    public static function getTypeCouriersArrayDefault()
    {
        return [
            self::TYPE_COURIER_DAY => 'Дневной',    
            self::TYPE_COURIER_EVENING => 'Вечерний',
        ];
    }
    
    public function getIsOpened() 
    {
        return $this->state === self::STATE_OPENED;
    }
    
    public function getIsClosed() 
    {
        return $this->state === self::STATE_CLOSED;
    }
    
    public function getIsTypeCourierDay() 
    {
        return $this->type_courier === self::TYPE_COURIER_DAY;
    }
    
    public function getIsTypeCourierEvening() 
    {
        return $this->type_courier === self::TYPE_COURIER_EVENING;
    }
    
    public function getIsTypeCourierAdditional() 
    {
        return $this->type_courier === self::TYPE_COURIER_ADDITIONAL;
    }
    
    public function getIsTypeCourierPickup() 
    {
        return $this->type_courier === self::TYPE_COURIER_PICKUP;
    }
    
    public function getIsTypeCourierDefault()
    {
        return $this->isTypeCourierDay || $this->isTypeCourierEvening;
    }
    
    public function getCashRequired()
    {
        if ($this->isTypeCourierDay || $this->isTypeCourierEvening) {
            return $this->check_sum - $this->check_nocash + $this->gifts - $this->spend;
        } elseif ($this->isTypeCourierAdditional) {
            return $this->check_sum - $this->check_nocash + $this->gifts;
        } elseif ($this->isTypeCourierPickup)  {
            return $this->check_sum - $this->check_nocash + $this->gifts;
        }
        
        return null;
    }
    
    public function getCashBalance() 
    {
        return ($this->cashRequired - $this->cash);
    }
    
    public function getDebt() 
    {
        //if ($this->isTypeCourierPickup) {
        //    return 0;
        //}
        $result = $this->check_sum;
        $result -= $this->check_nocash;
        if (!$this->isTypeCourierAdditional) {
            $result -= $this->spend;
        }
        $result -= $this->gifts;
        $result -= $this->cash;
        
        return $result;
    }
    
    
    public function open() 
    {
        $this->date_open = PickerApi::getCurrentTimestamp();
        $this->state = self::STATE_OPENED;
    }
    
    public function close()
    {
        $this->date_close = PickerApi::getCurrentTimestamp();
        $this->state = self::STATE_CLOSED;
    }
    
    public function openPickup() 
    {
        $this->date_open = PickerApi::getCurrentTimestamp();
        $this->state = self::STATE_OPENED;
        $this->type_courier = self::TYPE_COURIER_PICKUP;
        $this->courier_id = null;
        $this->courier_name = self::PICKUP_COUIRER_NAME;
        $this->courier_phone = null;
    }
    
    public function calcPayment(
        $payDayCourier, 
        $payEvenCourier, 
        $payDopCourier, 
        $payTrip
    ) {
        if ($this->isTypeCourierDay) {
            $this->payment = 
                ($this->count_order * $payDayCourier) +
                ($this->count_trip * $payTrip) - 
                ($this->debt);
        } elseif ($this->isTypeCourierEvening) {
            $this->payment = 
                ($this->count_order * $payEvenCourier) +
                ($this->count_trip * $payTrip) - 
                ($this->debt);
        } elseif ($this->isTypeCourierAdditional) {
            $this->payment = 
                ($this->count_order * $payDopCourier) +
                ($this->debt);
        } elseif ($this->isTypeCourierPickup) {
            $this->payment = 0;
        }
        return $this->payment;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(Shifts::className(), ['id' => 'shifts_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShiftPicker()
    {
        return $this->hasOne(ShiftsPicker::className(), ['id' => 'shifts_picker_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanknotes()
    {
        return $this->hasOne(Banknotes::className(), ['shifts_courier_id' => 'id']);
    }
    
}
