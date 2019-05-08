<?php

namespace app\modules\orders\models;

use Yii;

use yii\helpers\Json;
use yii\helpers\ArrayHelper;

use app\modules\orders\helpers\DateHelper;
use app\modules\orders\helpers\TimeHelper;
use app\modules\orders\helpers\DateTimeHelper;

/**
 * This is the model class for table "{{%orders_item_log}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $state
 * @property integer $station
 * @property integer $number
 * @property integer $date_start
 * @property integer $date_prepartion
 * @property integer $date_complete
 * @property integer $date_added
 * @property integer $date_end
 * @property integer $date_canceled
 *
 * @property OrdersOrders $order
 */
class OrderItemLog extends \yii\db\ActiveRecord
{
    const STATE_NEW = 0;
    const STATE_PREPARING = 1;
    const STATE_PREPARED = 2;
    const STATE_COMPLETE = 3;
    const STATE_ADDED = 4;
    const STATE_END = 6;
    const STATE_CANCELED = 7;
    
    
    public static function tableName()
    {
        return '{{%orders_item_log}}';
    }

 
    public function rules()
    {
        return [
            ['order_id', 'required'],
            ['order_id', 'integer'],
            [
                'order_id', 
                'exist',
                'skipOnError' => true, 
                'targetClass' => Order::className(), 
                'targetAttribute' => ['order_id' => 'id']
            ],
            
            ['product_id', 'required'],
            ['product_id', 'integer'],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
            
            ['station', 'required'],
            ['station', 'integer'],
            
            ['number', 'integer'],
            
            ['date_start', 'integer'],
            
            ['date_preparation', 'integer'],
            
            ['date_complete', 'integer'],

            ['date_pick_start', 'integer'],
            
            ['date_added', 'integer'],
            
            ['date_end', 'integer'],
            
            ['date_canceled', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'product_id' => 'Продукт',
            'state' => 'Состояние',
            'station' => 'Станция',
            'number' => 'Номер',
            'date_start' => 'Время начала готовки',
            'date_prepartion' => 'Время приготовления',
            'date_complete' => 'Время передачи сборщику',
            'date_pick_start' => 'Время начала сборки',
            'date_added' => 'Время сборки',
            'date_end' => 'Время передачи курьеру',
            'date_canceled' => 'Время отмены',
        ];
    }
  
    public static function getStatesArray()
    {
        return [
            self::STATE_NEW => 'Новый',
            self::STATE_PREPARING => 'Готовиться',
            self::STATE_PREPARED => 'Приготовлен',
            self::STATE_COMPLETE => 'Передан комплектовщику',
            self::STATE_ADDED => 'Собран',
            self::STATE_END => 'Передан курьеру',
            self::STATE_CANCELED => 'Отменён',
        ];
    }
    
    public static function getStatesAsJson()
    {
        $result = new \stdClass();
        $result->new = self::STATE_NEW;
        $result->preparing = self::STATE_PREPARING;
        $result->prepared = self::STATE_PREPARED;
        $result->complete = self::STATE_COMPLETE;
        $result->added = self::STATE_ADDED;
        $result->end = self::STATE_END;
        $result->canceled = self::STATE_CANCELED;
        return Json::encode($result);
    }
    
    public function getStateName()
    {
        return self::getStatesArray()[$this->state];
    }
    
    public function getIsNew()
    {
        return $this->state === self::STATE_NEW;
    }
    
    public function getIsPreparing()
    {
        return $this->state === self::STATE_PREPARING;
    }
    
    public function getIsPrepared()
    {
        return $this->state === self::STATE_PREPARED;
    }
    
    public function getIsComplete()
    {
        return $this->state === self::STATE_COMPLETE;
    }
    
    public function getIsAdded()
    {
        return $this->state === self::STATE_ADDED;
    }
    
    public function getIsEnd()
    {
        return $this->state === self::STATE_END;
    }
    
    public function getIsCanceled()
    {
        return $this->state === self::STATE_CANCELED;
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItem()
    {
        return $this->hasOne(OrderItem::className(), [
            'order_id' => 'order_id',
            'product_id' => 'product_id',
        ]);
    }
    
    public static function getOrderSelectableFields()
    {
        return [
            'id', 
            'order_num',
            'stage_id', 
            'person_num',
            'delivery_date', 
            'delivery_time',
        ];
    }
    
    public static function getOrdersForStation($cityId, $station) 
    {
        // Получим количество заказов на странице.
        $stationOrdersCount = OptionVal::getStationOrdersCount($cityId);
        $limit = $stationOrdersCount;
     
        return static::getOrdersForStationCommon($cityId, $station, $limit, false);
    }
     
    public static function getOrdersForStationAdditional($cityId, $station, $orderIds)
    {
        $mandatoryOrdersIds = self::getOrdersForStationOnIds($orderIds, $station, true);
        
        // Получим количество заказов на странице.
        $stationOrdersCount = OptionVal::getStationOrdersCount($cityId);
        $needAdditionalOrdersCount = $stationOrdersCount - count($mandatoryOrdersIds);
        
        $limit = $stationOrdersCount;
        $additionalOrderIds = self::getOrdersForStationCommon($cityId, $station, $limit, true); 
        $uniqueAdditionalOrderIds = array_diff($additionalOrderIds, $mandatoryOrdersIds);
        
        $needAddtionalOrderIds = array_slice($uniqueAdditionalOrderIds, 0, $needAdditionalOrdersCount);
        
        $allOrdersIds = ArrayHelper::merge($mandatoryOrdersIds, $needAddtionalOrderIds);
        $allOrders = self::getOrdersForStationOnIds($allOrdersIds, $station, false); 
        return $allOrders;
    }
    
    public static function getOrderForStation($orderId, $station)
    {
        $query = Order::find()
            ->select(self::getOrderSelectableFields())
            ->where(['id' => $orderId])
            ->with([
                'orderItemLogs' => function($q) use ($station) {
                    $q->where(['station' => $station]);
                    $q->with(['orderItem']); 
                },
            ]); 
        return $query->one();
    }
    
    public static function startCardInWork($order, $station) 
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            if (!($order->isNew  || $order->isAccepted || $order->isInWork)) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$order->isInWork) {
                $inWorkStageId = Stage::getInWorkStageId();
                $order->stage_id = $inWorkStageId;
                $order->update_date = $currentTime;
                if (!$order->save()) {
                    $transaction->rollBack();
                    return false;
                }
                
                $logRecord = new LogRecord();
                $logRecord->order_id = $order->id;
                $logRecord->stage_id =  $inWorkStageId;
                $logRecord->date = $currentTime;   
                if (!$logRecord->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

 
            $orderItemLogs = $order->getOrderItemLogs()
                ->where(['station' => $station])
                ->all();
            
            if (
                count($orderItemLogs) > 0 && 
                !$orderItemLogs[0]->isPreparing
            ) {
                $number = Number::getNumber($order->city_id, $station, $order->id);
                foreach ($orderItemLogs as $orderItemLog) {
                    $orderItemLog->state = OrderItemLog::STATE_PREPARING;
                    $orderItemLog->date_start = $currentTime;
                    $orderItemLog->number = $number;
                    if (!$orderItemLog->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }

            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function completeCard($order, $station) 
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            if (!$order->isInWork) {
                $transaction->rollBack();
                return false;
            }
            
            $orderItemLogs = $order->getOrderItemLogs()
                ->where(['station' => $station])
                ->all();
            
            if (count($orderItemLogs) > 0) {
                foreach ($orderItemLogs as $orderItemLog) {
                    if (!$orderItemLog->isPrepared) {
                        $transaction->rollBack();
                        return false;
                    }
                }
                
                //Number::freeNumber($order->city_id, $station, $orderItemLogs[0]->number); 
                foreach ($orderItemLogs as $orderItemLog) {
                    $orderItemLog->state = OrderItemLog::STATE_COMPLETE;
                    $orderItemLog->date_complete = $currentTime;
                    if (!$orderItemLog->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }                  
            }

            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function cancelCard($order, $station)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            if (!$order->isCanceled) {
                $transaction->rollBack();
                return false;
            }
            
            $orderItemLogs = $order->getOrderItemLogs()
                ->where(['station' => $station])
                ->all();
            
            if (count($orderItemLogs) > 0) {
                $numberValue = null;
                foreach ($orderItemLogs as $orderItemLog) {
                    if ($orderItemLog->number !== null) {
                        $numberValue = $orderItemLog->number;
                    }
                    
                    $orderItemLog->state = OrderItemLog::STATE_CANCELED;
                    $orderItemLog->date_canceled = $currentTime;
                    $orderItemLog->number = null;
                    if (!$orderItemLog->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
                
                if ($numberValue !== null) {
                    if (!Number::freeNumber($numberValue, $order->city_id, $station)) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }

            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function setProductPreparing($orderItemLog)
    {
        $transaction = static::getDb()->beginTransaction();
        try {           
            if ($orderItemLog->isPreparing) { 
                $transaction->rollBack();
                return true;
            }
            
            if (!$orderItemLog->isPrepared) {
                $transaction->rollBack();
                return false;    
            }
            
            $orderItemLog->state = OrderItemLog::STATE_PREPARING;
            $orderItemLog->date_preparation = null;
            
            if (!$orderItemLog->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function setProductPrepared($orderItemLog) 
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
           
            if ($orderItemLog->isPrepared) { 
                $transaction->rollBack();
                return true;
            }
            
            if (!$orderItemLog->isPreparing) {
                $transaction->rollBack();
                return false;    
            }
            
            $orderItemLog->state = OrderItemLog::STATE_PREPARED;
            $orderItemLog->date_preparation = $currentTime;
            
            if (!$orderItemLog->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function getOrdersForStationPick($cityId)
    {
        return self::getOrdersForStationPickCommon($cityId);
    }
    
    public static function getOrderForStationPick($orderId)
    {
        $query = Order::find()
            ->select(self::getOrderSelectableFields())
            ->where(['id' => $orderId])
            ->with([
                'orderItemLogs' => function($q) {
                    $q->with(['orderItem']); 
                },
            ]); 
        return $query->one();
    }
    
    public static function startCardInPick($order)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            if (!($order->isAccepted || $order->isInWork)) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$order->isInWork) {
                $inWorkStageId = Stage::getInWorkStageId();
                $order->stage_id = $inWorkStageId;
                $order->update_date = $currentTime;
                if (!$order->save()) {
                    $transaction->rollBack();
                    return false;
                }
                
                $logRecord = new LogRecord();
                $logRecord->order_id = $order->id;
                $logRecord->stage_id =  $inWorkStageId;
                $logRecord->date = $currentTime;   
                if (!$logRecord->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

 
            $orderItemLogs = $order->getOrderItemLogs()->all();
            $pickStationId = Station::getPickStationId();
            if (count($orderItemLogs) > 0) {
                if ($orderItemLogs[0]->date_pick_start !== null) {
                    $transaction->rollBack();
                    return false;
                }
            }
                
            foreach ($orderItemLogs as $orderItemLog) {
                if ($orderItemLog->station === $pickStationId) {
                    $orderItemLog->state = OrderItemLog::STATE_PREPARING;
                    $orderItemLog->date_start = $currentTime;
                }

                $orderItemLog->date_pick_start = $currentTime;
                if (!$orderItemLog->save()) {
                    $transaction->rollBack();
                    return false;
                }                
            }
           

            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function deliverCardPick($order)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            if (!$order->isInWork) {
                $transaction->rollBack();
                return false;
            }
            
            $orderItemLogs = $order->getOrderItemLogs()->all();
            if (count($orderItemLogs) > 0) {
                if ($orderItemLogs[0]->date_pick_start === null) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            foreach ($orderItemLogs as $orderItemLog) {
                if (!$orderItemLog->isAdded) {
                    $transaction->rollBack();
                    return false;
                }

                // Запомним время передачи курьеру.
                $orderItemLog->date_end = $currentTime; 
                if (!$orderItemLog->save()) {
                    $transaction->rollBack();
                    return false; 
                }
            }
            

            $deliveringStageId = Stage::getDeliveringStageId();
            $order->stage_id = $deliveringStageId;
            $order->update_date = $currentTime;
            if (!$order->save()) {
                $transaction->rollBack();
                return false;
            }
                
            $logRecord = new LogRecord();
            $logRecord->order_id = $order->id;
            $logRecord->stage_id =  $deliveringStageId;
            $logRecord->date = $currentTime;   
            if (!$logRecord->save()) {
                $transaction->rollBack();
                return false;
            }
           
            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function cancelCardPick($order)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            if (!$order->isCanceled) {
                $transaction->rollBack();
                return false;
            }
            
            $pickStationId = Station::getPickStationId();
            $orderItemLogs = $order->getOrderItemLogs()->all();
            

            foreach ($orderItemLogs as $orderItemLog) {
                if ($orderItemLog->station !== $pickStationId) {
                    if (
                        !$orderItemLog->isComplete &&
                        !$orderItemLog->isNew &&
                        !$orderItemLog->isCanceled
                    ) {
                        $transaction->rollBack();
                        return false;
                    }
                }
                
                if (!$orderItemLog->isCanceled) {
                    $orderItemLog->state = OrderItemLog::STATE_CANCELED;
                    $orderItemLog->date_canceled = $currentTime;
                }
                
                if (!$orderItemLog->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if (!Number::freeNumbersOnOrder($order)) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function setProductAdded($orderItemLog)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
           
            if ($orderItemLog->isAdded) { 
                $transaction->rollBack();
                return true;
            }
            
            $isKitchenOrderItemLog = $orderItemLog->station !== Station::getPickStationId();            
            if ($isKitchenOrderItemLog) {
                if (!$orderItemLog->isComplete) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if ($isKitchenOrderItemLog) {
                $orderItemLog->state = OrderItemLog::STATE_ADDED;
                $orderItemLog->date_added = $currentTime;                
                if (!Number::freeNumberOnOrderItemLog($orderItemLog)) {
                    $transaction->rollBack();
                    return false;
                }
                $orderItemLog->number = null;
            } else {
                $orderItemLog->state = OrderItemLog::STATE_ADDED;
                $orderItemLog->date_preparation = $currentTime;
                $orderItemLog->date_added = $currentTime;
            }
            
            if (!$orderItemLog->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }   
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    private static function getOrdersForStationCommon(
        $cityId, 
        $station, 
        $limit = null, 
        $selectOnlyIds = false
    ) {        
        // Нужны заказы только на определённых стадиях.
        $excludedOrderStageIds = self::getExcludedOrderStatgeIdsForStation();
        $possibleOrderItemLogStates = self::getPossibleOrderItemLogsStatesForStation();
    
        if ($selectOnlyIds) {
            $select = [Order::tableName().'.id'];    
        } else {
            foreach (self::getOrderSelectableFields() as $fieldName) {
                $select []= Order::tableName().'.'.$fieldName;
            }
        }
        
        $query = Order::find()
            ->select($select)
            ->where(['city_id' => $cityId])
            ->andWhere(['not in', 'stage_id', $excludedOrderStageIds]);
        

        $currentTime = OrdersApi::getCurrentTimestamp();
        $fromDeliveryDate = DateHelper::getDateDbFormatFromTimestamp($currentTime);
        $fromDeliveryTime = TimeHelper::getTimeDbFormatFromTimestamp($currentTime);
        $query->andWhere([
           'or', 
           [
               'and', 
               ['=', 'delivery_date', $fromDeliveryDate], 
               ['>=', 'delivery_time', $fromDeliveryTime],
           ],
           ['>', 'delivery_date', $fromDeliveryDate], 
        ]); 

        
        if ($selectOnlyIds) {
            $loadRelations  = false;
        } else {
            $loadRelations = true;
        }
                   
        // Используем INNER JOIN так как заказы в которых нету продуктов 
        // для нужной станции нам не нужны.    
        $query->innerJoinWith([
            'orderItemLogs' => function($q) use ($station, $possibleOrderItemLogStates, $selectOnlyIds) {
                $q->onCondition([
                    'and',
                    ['in', 'state', $possibleOrderItemLogStates], 
                    ['station' => $station],
                ]);
                // Информация о продукте также понадобиться, так как 
                // нужно знать количество.
                if (!$selectOnlyIds) {
                    $q->with(['orderItem']);
                }
            },
        ], $loadRelations);
                
        // Сгруппируем, чтобы правильно работали LIMIT и OFFSET - не зависели от 
        // количества блюд в заказе.
        $query->groupBy(Order::tableName().'.id'); 
        $query->orderBy([
            'delivery_date' => SORT_ASC, 
            'delivery_time' => SORT_ASC
        ]);
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        if ($selectOnlyIds) {
            return $query->column();
        } else {
            return $query->all();
        }
    }
    
    private static function getOrdersForStationOnIds($orderIds, $station, $selectOnlyIds = false)
    {
         // Нужны заказы только на определённых стадиях.
        $excludedOrderStageIds = self::getExcludedOrderStatgeIdsForStation();
        $possibleOrderItemLogStates = self::getPossibleOrderItemLogsStatesForStation();
        
        $select = [];
        if ($selectOnlyIds) {
            $select = [Order::tableName().'.id'];
        } else {
            foreach (self::getOrderSelectableFields() as $fieldName) {
                $select []= Order::tableName().'.'.$fieldName;
            }
        }
        
        $query = Order::find()
            ->select($select)
            ->andWhere(['not in', 'stage_id', $excludedOrderStageIds]);

        $query->andWhere(['in', Order::tableName().'.id', $orderIds]); 
                   
        if ($selectOnlyIds) {
            $loadRelations  = false;
        } else {
            $loadRelations = true;
        }
        
        // Используем INNER JOIN так как заказы в которых нету продуктов 
        // для нужной станции нам не нужны.
        $query->innerJoinWith([
            'orderItemLogs' => function($q) use ($station, $possibleOrderItemLogStates, $selectOnlyIds) {
                $q->onCondition([
                    'and',
                    ['in', 'state', $possibleOrderItemLogStates], 
                    ['station' => $station],
                ]);
                // Информация о продукте также понадобиться, так как 
                // нужно знать количество.
                if (!$selectOnlyIds) {
                    $q->with(['orderItem']); 
                }
            },
        ], $loadRelations);
                
        // Сгруппируем, чтобы правильно работали LIMIT и OFFSET - не зависели от 
        // количества блюд в заказе.
        $query->groupBy(Order::tableName().'.id'); 
        $query->orderBy([
            'delivery_date' => SORT_ASC, 
            'delivery_time' => SORT_ASC
        ]);
       
        if ($selectOnlyIds) {
            return $query->column();
        } else {
            return $query->all();
        }
    }
    
    private static function getOrdersForStationPickCommon($cityId) {        
        // Нужны заказы только на определённых стадиях.
        $excludedOrderStageIds = self::getExcludedOrderStatgeIdsForStationPick();
        $possibleOrderItemLogStates = self::getPossibleOrderItemLogsStatesForStationPick();
        
        $select = [];    
        foreach (self::getOrderSelectableFields() as $fieldName) {
            $select []= Order::tableName().'.'.$fieldName;
        }
        
        $query = Order::find()
            ->select($select)
            ->where(['city_id' => $cityId])
            ->andWhere(['not in', 'stage_id', $excludedOrderStageIds]);
        
        $currentTime = OrdersApi::getCurrentTimestamp();
        $fromDeliveryDate = DateHelper::getDateDbFormatFromTimestamp($currentTime);
        $fromDeliveryTime = TimeHelper::getTimeDbFormatFromTimestamp($currentTime);
        $query->andWhere([
           'or', 
           [
               'and', 
               ['=', 'delivery_date', $fromDeliveryDate], 
               ['>=', 'delivery_time', $fromDeliveryTime],
           ],
           ['>', 'delivery_date', $fromDeliveryDate], 
        ]); 
              
        // Используем INNER JOIN так как заказы в которых нету продуктов 
        // для нужной станции нам не нужны.    
        $query->innerJoinWith([
            'orderItemLogs' => function($q) use ($possibleOrderItemLogStates) {
                $q->onCondition([
                    'and',
                    ['in', 'state', $possibleOrderItemLogStates], 
                ]);
                // Информация о продукте также понадобиться, так как 
                // нужно знать количество.
                $q->with(['orderItem']);
                
            },
        ]);
                
        // Сгруппируем, чтобы правильно работали LIMIT и OFFSET - не зависели от 
        // количества блюд в заказе.
        $query->groupBy(Order::tableName().'.id'); 
        $query->orderBy([
            'delivery_date' => SORT_ASC, 
            'delivery_time' => SORT_ASC
        ]);
   
        $orders = $query->all();

        
        $resultOrders = [];
        foreach ($orders as $order) {
            $orderItemLogs = $order->orderItemLogs;
            $isSuitable = false;
            foreach ($orderItemLogs as $orderItemLog) {
                if (!$orderItemLog->isCanceled) {
                    $isSuitable= true;
                    break;
                 }
            }
            if ($isSuitable) {
                $resultOrders []= $order;
            }
        }
        
        return $resultOrders;
    }
    
    
    private static function getExcludedOrderStatgeIdsForStation()
    {
        $newStageId = Stage::getNewStageId();
        $deliveringStageId = Stage::getDeliveringStageId();
        $deliveredStageId = Stage::getDeliveredStageId();
    
        return $excludedOrderStageIds = [
            $newStageId,
            $deliveringStageId, 
            $deliveredStageId,
        ];
    }
    
    private static function getExcludedOrderStatgeIdsForStationPick()
    {
        $newStageId = Stage::getNewStageId();
        $deliveringStageId = Stage::getDeliveringStageId();
        $deliveredStageId = Stage::getDeliveredStageId();
    
        return $excludedOrderStageIds = [
            $newStageId,
            $deliveringStageId, 
            $deliveredStageId,
        ];
    }
    
    private static function getPossibleOrderItemLogsStatesForStation()
    {
        return [
            self::STATE_NEW, 
            self::STATE_PREPARING, 
            self::STATE_PREPARED,
        ];
    }
    
    private static function getPossibleOrderItemLogsStatesForStationPick()
    {
        return [
            self::STATE_NEW, 
            self::STATE_PREPARING, 
            self::STATE_PREPARED,
            self::STATE_COMPLETE,
            self::STATE_ADDED,
            self::STATE_CANCELED,
        ];
    }
    
    
}
