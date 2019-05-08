<?php

namespace app\modules\orders\models;

use yii\db\Expression;

/**
 * This is the model class for table "{{%orders_number}}".
 *
 * @property integer $number
 * @property integer $city_id
 * @property integer $free
 */
class Number extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%orders_number}}';
    }


    public function rules()
    {
        return [
            ['number', 'required'],
            ['number', 'integer'],
            
            ['city_id', 'required'],
            ['city_id', 'integer'],
            
            ['station_id', 'required'],
            ['station_id', 'integer'],
            
            ['order_id', 'integer'],
            
            ['date', 'integer'],
            
            ['free', 'boolean'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'number' => 'Номер',
            'city_id' => 'Город',
            'station_id' => 'Станция',
            'order_id' => 'Заказ',
            'date' => 'Дата',
            'free' => 'Свободен',
        ];
    }
    
    public function getCityName()
    {
        return OrdersApi::getCityNameById($this->city_id);
    }
    
    public static function getNumber($cityId, $stationId, $orderId) 
    {
        $currentTime = OrdersApi::getCurrentTimestamp();
        
        $minFreeNumber = self::find()
            ->select(new Expression('MIN(number)'))
            ->where([
                'city_id' => $cityId,
                'station_id' => $stationId,
                'free' => true
            ])
            ->scalar();
        
        if ($minFreeNumber === null) {
            $newNumber = new Number();
            $newNumber->city_id = $cityId;
            $newNumber->station_id = $stationId;
            $newNumber->date = $currentTime;
            $newNumber->free = false;
            
            $maxNumber = self::find()
                ->select(new Expression('MAX(number)'))
                ->where([
                    'city_id' => $cityId,
                    'station_id' => $stationId,
                ])
                ->scalar();
            
            
            if ($maxNumber === null) {
                $newNumber->number = 1;
            } else {
                $newNumber->number = $maxNumber + 1;
            }
            
            $newNumber->order_id = $orderId;
            
            if (!$newNumber->save()) {
                throw new \Exception();
            }
            
            $result = (int) $newNumber->number;
        } else {
            $updatedRowsCount = Number::updateAll(
                [
                    'free' => false,
                    'order_id' => $orderId,
                    'date' => $currentTime,
                ], 
                [
                    'city_id' => $cityId,
                    'station_id' => $stationId,
                    'number' => $minFreeNumber,
                ]
            );
            
            if ($updatedRowsCount !== 1) {
                throw new \Exception();
            }
                
            $result = (int) $minFreeNumber;
        }
        
        return $result;
    }
 
    public static function freeNumber($numberValue, $cityId, $stationId) 
    {
        Number::updateAll(
            [
                'free' => true,
                'order_id' => null,
                'date' => null,
            ], 
            [
                'city_id' => $cityId,
                'station_id' => $stationId,
                'number' => $numberValue,
                'free' => false,

            ]
        );
        
        return true;
    }
    
    public static function freeNumbersOnOrder($order) 
    {
        Number::updateAll(
            [
                'free' => true, 
                'order_id' => null, 
                'date' => null,
            ], 
            [
                'order_id' => $order->id,
                'free' => false,
            ]
        );
        
        return true;
    }
    
    public static function freeNumberOnOrderItemLog($orderItemLog)
    {
        $order = $orderItemLog->getOrder()
                ->select(['id', 'city_id'])
                ->one();
        
        $numberValue = $orderItemLog->number;
        $stationId = $orderItemLog->station;
        $cityId = $order->city_id;
        
        $remainingOrderItemLogsCount = (int) OrderItemLog::find()
            ->select([new Expression('COUNT(*)')])
            ->where([
                'order_id' => $orderItemLog->order_id,
                'station' => $stationId,
                'number' => $numberValue,
            ])
            ->andWhere(['<>', 'product_id', $orderItemLog->product_id])
            ->scalar();
        
        if ($remainingOrderItemLogsCount === 0) {
            $updatedRowsCount = Number::updateAll(
                [
                    'free' => true, 
                    'order_id' => null, 
                    'date' => null,
                ], 
                [
                    'station_id' => $stationId,
                    'city_id' => $cityId,
                    'number' => $numberValue,
                    'free' => false,
                ]
            );
            
            return ($updatedRowsCount > 0);
        } else {
            return true;
        }
    }
    
}
