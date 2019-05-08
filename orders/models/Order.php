<?php

namespace app\modules\orders\models;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

use app\modules\orders\helpers\DateHelper;
use app\modules\orders\helpers\TimeHelper;
use app\modules\orders\models\DateTimeConstsInterface;

/**
 * This is the model class for table "{{%orders_orders}}".
 *
 * @property integer $id
 * @property string $order_num
 * @property integer $client_id
 * @property string $recipient
 * @property string $phone
 * @property string $alter_phone
 * @property integer $user_id
 * @property integer $start_date
 * @property integer $update_date
 * @property integer $end_date
 * @property integer $stage_id
 * @property integer $payment_type
 * @property integer $is_paid
 * @property integer $tax
 * @property integer $items_count
 * @property integer $total_price
 * @property integer $total_pay
 * @property integer $is_deleted
 * @property string $address
 * @property integer $person_num
 * @property string $comment
 * @property integer $is_postponed
 * @property string $delivery_date
 * @property string $delivery_time
 * @property integer $city_id
 * @property integer $return_sum
 *
 * @property DeliveryInfo $deliveryInfo
 * @property OrderItem[] $ordersItems
 * @property LogRecord[] $logRecords
 * @property PaymentType $paymentType
 * @property Stage $stage
 */
class Order extends \yii\db\ActiveRecord implements DateTimeConstsInterface
{    
    const SCENARIO_NAME_INPUT = 'nameInput';
    const SCENARIO_PHONE_INPUT = 'phoneInput';
    const SCENARIO_ADDRESS_INPUT = 'addressInput';
    const SCENARIO_INFO_INPUT = 'orderInfoInput';
    const SCENARIO_POSTPONED_ORDER_DATE_INPUT = 'postponedOrderDateInput';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_orders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['order_num', 'required'],
            ['order_num', 'string', 'max' => 255],
            
            ['recipient', 'required', 'on' => [self::SCENARIO_NAME_INPUT]],
            ['recipient', 'string', 'max' => 255],
            
            ['user_id', 'integer'],
            
            ['city_id', 'integer'],
            
            ['client_id', 'integer'],
            
            ['start_date', 'required'],
            ['start_date', 'integer'],
            
            ['update_date', 'integer'],
            
            ['end_date', 'integer'],
            
            ['stage_id', 'required'],
            ['stage_id', 'integer'],
            [
                'stage_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Stage::className(), 
                'targetAttribute' => ['stage_id' => 'id']
            ],
            
            ['payment_type', 'integer'],
            [
                'payment_type', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => PaymentType::className(), 
                'targetAttribute' => ['payment_type' => 'id']
            ],
            ['payment_type', 'default', 'value' => PaymentType::getCashToCourierPaymentTypeId()],
            
            
            ['phone', 'required', 'on' => [
                self::SCENARIO_DEFAULT,
                self::SCENARIO_UPDATE,
                self::SCENARIO_PHONE_INPUT,
            ]],        
            ['phone', 'string', 'max' => 255],
            ['phone', 'match', 'pattern' => '/^[0-9]+$/'],
            
            ['alter_phone', 'string', 'max' => 255],
            ['alter_phone', 'match', 'pattern' => '/^[0-9]+$/'],
                
            ['address', 'required', 'on' => [self::SCENARIO_ADDRESS_INPUT]],
            
            ['address_json', 'safe'],
            
            ['items_count', 'required'],
            ['items_count', 'integer'],
                        
            ['total_price', 'integer'],
            
            ['tax', 'integer'],
            ['tax', 'default', 'value' => 0],
            
            ['total_pay', 'integer'],
            
            ['return_sum', 'integer'],
            ['return_sum', 'default', 'value' => 0],
          
            ['person_num', 'integer'],
            ['person_num', 'default', 'value' => 1],

            ['is_paid', 'boolean'],
            ['is_paid', 'default', 'value' => false],
            
            ['is_deleted', 'boolean'],
            ['is_deleted', 'default', 'value' => false],
            
            ['is_postponed', 'boolean'],
            ['is_postponed', 'default', 'value' => false],
            
            ['delivery_date', 'required', 'on' => self::SCENARIO_POSTPONED_ORDER_DATE_INPUT],
            ['delivery_date', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['delivery_time', 'required', 'on' => self::SCENARIO_POSTPONED_ORDER_DATE_INPUT],
            ['delivery_time', 'date', 'format' => self::TIME_SHORT_FORMAT_YII],

            ['comment', 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_num' => 'Номер',
            'client_id' => 'Клиент',
            'recipient' => 'Получатель',
            'phone' => 'Телефон',
            'alter_phone' => 'Дополнительный телефон',
            'user_id' => 'Пользователь',
            'start_date' => 'Дата создания',
            'update_date' => 'Дата изменения',
            'end_date' => 'Дата окончания',
            'stage_id' => 'Стадия',
            'payment_type' => 'Тип оплаты',
            'is_paid' => 'Оплачен',
            'tax' => 'Величина скидки',
            'items_count' => 'Количество позиций',
            'total_price' => 'Сумма(без скидки)',
            'total_pay' => 'Сумма (к оплате)',
            'is_deleted' => 'Отменён',
            'address' => 'Адрес',
            'address_json' => 'Адресс в Json',
            'person_num' => 'Количество персон',
            'comment' => 'Комментарий',
            'is_postponed' => 'Отложенный',
            'delivery_date' => 'Дата доставки',
            'delivery_time' => 'Время доставки',
            'city_id' => 'Город',
            'return_sum' => 'Сдача',
            'stageName' => 'Стадия',
            'paymentTypeName' => 'Тип оплаты',
        ];
    }
    
    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
        ];
    }
    

    public function scenarios()
    {
        $scenarios = parent::scenarios();
           
        $scenarios[self::SCENARIO_NAME_INPUT] = [
            'recipient',
        ];
        
        $scenarios[self::SCENARIO_PHONE_INPUT] = [
            'phone',
            'alter_phone'
        ];
        
        $scenarios[self::SCENARIO_ADDRESS_INPUT] = [
            'address',
            '!address_josn',
        ];
        
        $scenarios[self::SCENARIO_INFO_INPUT] = [
            'person_num',
            'return_sum',
            'payment_type',
            'is_postponed',
            'delivery_date',
            'delivery_time',
        ];
        
        $scenarios[self::SCENARIO_POSTPONED_ORDER_DATE_INPUT] = [
            '!delivery_date',
            '!delivery_time',
        ];
        
        $scenarios[self::SCENARIO_UPDATE] = [
            'recipient',
            'phone',
            'alter_phone',
            'person_num',
            'payment_type',
            'city_id',
            'address',
            '!address_json',
            'comment',
        ];
                
        return $scenarios;
    }
    
    public function getIsNew()
    { 
        return $this->stage_id === Stage::getNewStageId();
    }
    
    public function getIsAccepted()
    {
        return $this->stage_id === Stage::getAcceptedStageId();
    }
    
    public function getIsInWork()
    {
        return $this->stage_id === Stage::getInWorkStageId();
    }
    
    public function getIsDelivering()
    {
        return $this->stage_id === Stage::getDeliveringStageId();
    }
    
    public function getIsDelevered()
    {
        return $this->stage_id === Stage::getDeliveredStageId();
    }
    
    public function getIsCanceled()
    {
        return $this->stage_id === Stage::getCanceledStageId();
    }
     
    public function isExpired()
    {
        if (!$this->isNew) {
            return false;
        }
        
        if ($this->city_id === null) {
            $cityId = OrdersApi::getDefaultCityId(); 
        } else {
            $cityId = $this->city_id;
        }
        $expiryTime = (int) OptionVal::getExpiryTimeValue($cityId) * 60;
        
        $currentTime = OrdersApi::getCurrentTimestamp();
        if ($currentTime > ($this->start_date + $expiryTime)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getStageName()
    {
        return $this->stage->name;
    }
    
    public function getCityName()
    {
        if ($this->city_id === null) {
            return null;
        }
        return OrdersApi::getCityNameById($this->city_id);
    }
    
    public function getClientName()
    {
        if ($this->client_id === null) {
            return null;
        }
        
        return OrdersApi::getClientNameById($this->client_id);
    }
    
    public function getUserName()
    {
        if ($this->user_id === null) {
            return null;
        }
        
        return OrdersApi::getUserNameById($this->user_id);
    }
    
    public function getPaymentTypeName()
    {
        if ($this->paymentType === null) {
            return null;
        }
        
        return $this->paymentType->name;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryInfo()
    {
        return $this->hasOne(DeliveryInfo::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
    }
    
    public function getOrderItemLogs()
    {
        return $this->hasMany(OrderItemLog::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogRecords()
    {
        return $this->hasMany(LogRecord::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['id' => 'payment_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStage()
    {
        return $this->hasOne(Stage::className(), ['id' => 'stage_id']);
    }
    
    public function beforeDelete() 
    {
        $orderItems = $this->orderItems;
        foreach ($orderItems  as $orderItem) {
            if ($orderItem->delete() === false) {
                throw new \Exception();
            }
        }
        
        $deliveryInfo = $this->deliveryInfo;
        if ($deliveryInfo !== null) {
            if ($deliveryInfo->delete() === false) {
                throw new \Exception();
            }
        }
        
        $logRecords = $this->logRecords;
        foreach ($logRecords as $logRecord) {
            if ($logRecord->delete() === false) {
                throw new \Exception();
            }
        }
        
        $orderItemLogs = $this->orderItemLogs;
        foreach ($orderItemLogs as $orderItemLog) {
            if ($orderItemLog->delete() === false) {
                throw new \Exception();
            }
        }
        
        return parent::beforeDelete();
    }
    
    public function beforeSave($insert) 
    {
        if (!empty($this->delivery_date)) { 
            $this->delivery_date = DateHelper::convertDateToDbFormat($this->delivery_date, self::DATE_FORMAT);
        }
        
        if (!empty($this->delivery_time)) { 
            $this->delivery_time = TimeHelper::convertTimeToDbFormat($this->delivery_time, self::TIME_SHORT_FORMAT);
        }

        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->delivery_date)) {
            $this->delivery_date = DateHelper::convertDateFromDbFormat($this->delivery_date, self::DATE_FORMAT);
        }
        
        if (!empty($this->delivery_time)) {
            $this->delivery_time = TimeHelper::convertTimeFromDbFormat($this->delivery_time, self::TIME_SHORT_FORMAT);
        }
        
        parent::afterFind();
    }
    
    public static function createNewOrder($order)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            $stageId = Stage::getNewStageId();
            $order->order_num = OrdersApi::getOrderNum();
            $order->start_date = $currentTime;
            $order->update_date = $currentTime;
            $order->stage_id = $stageId;
            $order->is_paid = false;
            $order->is_deleted = false;
            $order->is_postponed = false;
            self::fillCartValuesInNewOrder($order, null);

            if (!$order->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $logRecord = new LogRecord();
            $logRecord->order_id = $order->id;
            $logRecord->stage_id = $stageId;
            $logRecord->date = $currentTime;
            $logRecord->comment = null;
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
    
    public static function setCityInOrder($order, $cityId)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            /*
            // Если город изменился то очищаем корзину, так как в другом 
            // городе у аналогичных продуктов другие id. Кроме того
            // какие-либо продукты могут отсутсвовать.
            $oldCityId = $order->city_id;
            if ($oldCityId === null || $cityId !== $oldCityId) {
                $orderItems = $order->getOrderItems()->all();
                foreach ($orderItems as $orderItem) {
                    if ($orderItem->delete() === false) {
                        $transaction->rollBack();
                        return false;
                    }
                }
                $order->total_price = 0;
                $order->total_pay = 0;
                $order->items_count = 0;
            }
            */
            
            $order->city_id = (int) $cityId;
            $order->update_date= OrdersApi::getCurrentTimestamp();
            if (!$order->save()) {
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
    
    public static function addProductToOrder($order, $productId)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $order->update_date = OrdersApi::getCurrentTimestamp();
            $product = OrdersApi::getProductById($productId);
            if ($product === null) {
                $transaction->rollBack();
                return false;
            }
            
            $orderItem = OrderItem::find()
                ->where([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                ])
                ->one();
            
            if ($orderItem !== null) {
                $orderItem->quantity = $orderItem->quantity + 1;
                self::setTotalPriceInOrderItem($orderItem, $order);
                if (!$orderItem->save()) {
                    $transaction->rollBack();
                    return false;
                }
            } else {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->price = $product->price;
                $orderItem->quantity = 1;
                self::setTotalPriceInOrderItem($orderItem, $order);
                if (!$orderItem->save()) {
                    $transaction->rollBack();
                    return false;  
                }
                
                // Добавим запись о состоянии блюда.
                $orderItemLog = new OrderItemLog();
                self::fillNewOrderItemLog($orderItemLog, $product);
                $orderItemLog->order_id = $order->id;
                if (!$orderItemLog->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            self::setCartValuesInOrder($order);
            if (!$order->save()) {
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
    
    public static function updateOrderItems($order, $productCounts)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $order->update_date = OrdersApi::getCurrentTimestamp();
            // Добавим продукты которых ранее не было.
            $existsIds = [];
            foreach ($order->orderItems as $orderItem) {
                $existsIds []= $orderItem->product_id;
            }
            foreach ($productCounts as $productId => $productCount) {
                if (!in_array($productId, $existsIds)) {
                    $product = OrdersApi::getProductById($productId);
                    if ($product === null) {
                        $transaction->rollBack();
                        return false;
                    }
                    
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->price = $product->price;
                    $orderItem->quantity = $productCount;
                    self::setTotalPriceInOrderItem($orderItem, $order);
                    if (!$orderItem->save()) {
                        $transaction->rollBack();
                        return false;    
                    }
                    
                    // Добавим запись о состоянии блюда.
                    $orderItemLog = new OrderItemLog();
                    self::fillNewOrderItemLog($orderItemLog, $product);
                    $orderItemLog->order_id = $order->id;
                    if (!$orderItemLog->save()) {
                        $transaction->rollBack();
                        return false;
                    }                 
                }
            }
            
            foreach ($order->orderItems as $orderItem) {
                if (!isset($productCounts[$orderItem->product_id])) {
                    // Если продукта теперь нет в корзине, удалим его.
                    if ($orderItem->delete() === false) {
                        $transaction->rollBack();
                        return false;
                    } 
                    // Также удалим и запись о его состоянии.
                    $orderItemLog = OrderItemLog::findOne([
                        'product_id' => $orderItem->product_id,
                        'order_id' => $orderItem->order_id
                    ]);
                    if ($orderItemLog !== null) {
                        if ($orderItemLog->delete() === false) {
                            $transaction->rollBack();
                            return false;   
                        }
                    }
                } else {
                    // Иначе обновим количество и общуюю цену.
                    $count = (int) $productCounts[$orderItem->product_id];
                    $orderItem->quantity = $count;
                    self::setTotalPriceInOrderItem($orderItem, $order);
                    if (!$orderItem->save()) {
                        $transaction->rollBack();
                        return false;
                    }          
                }
            }
                        
            self::setCartValuesInOrder($order);
            if (!$order->save()) {
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
    
    public static function saveChangesInOrder($order)
    {
        $order->update_date = OrdersApi::getCurrentTimestamp();
        return $order->save();
    }
    
    public static function setTaxFromBonusesInOrder($order, $tax)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $order->update_date = OrdersApi::getCurrentTimestamp();
            $previousTax = (int) Order::find()
                ->select('tax')
                ->where(['id' => $order->id])
                ->scalar();
            
            if ($order->client_id === null) {
                $transaction->rollBack();
                return false;
            }
            
            if (!OrdersApi::setTaxFromBonuses($order->client_id, $tax, $previousTax)) {
                $transaction->rollBack();
                return false;
            }
            
            $order->tax = (int) $tax;
            self::setTotalPayInOrder($order);

            if (!$order->save()) {
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
    
    public static function saveInfoInOrder($order)
    {
        $transaction = static::getDb()->beginTransaction();
        try {            
            $order->update_date = OrdersApi::getCurrentTimestamp();
            if ($order->is_postponed) {
                $currentScenario = $order->scenario;
                $order->scenario = self::SCENARIO_POSTPONED_ORDER_DATE_INPUT;
                if (!$order->validate(['delivery_date', 'delivery_time'])) {
                    $order->scenario = $currentScenario;
                    $transaction->rollBack();
                    return false;
                }
                
                $order->scenario = $currentScenario;
            } else {
                $order->delivery_date = null;
                $order->delivery_time = null;
            }
            
            $hasDeliveryInfo = $order->deliveryInfo !== null;
            if (!$hasDeliveryInfo) {
                $deliveryInfo = new DeliveryInfo();
                $deliveryInfo->order_id = $order->id;
            }  else {
                $deliveryInfo = $order->deliveryInfo;
            }
     
            if (!$hasDeliveryInfo) {
                self::setPriceInDeliveryInfo($deliveryInfo, $order); 
            }
                    
            if ($order->is_postponed) {
                self::copyDateTimeToDeliveryInfoFromOrder($deliveryInfo, $order);
            }
            
            // Если информация о доставке только создаётся и заказ 
            // не отложенный, то рассчитаем время доставки.
            if (!$order->is_postponed && !$hasDeliveryInfo) {
                self::setDateTimeInDeliveryInfoAndOrder($deliveryInfo, $order);
            }
            
            if (!$deliveryInfo->save()) {
                $transaction->rollBack();
                return false;
            }
                 
            if (!$order->save()) {
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
    
    public static function acceptOrder($order, $logRecord)
    { 
        $logRecord->stage_id = Stage::getAcceptedStageId();
        return self::updateStageInOrder($order, $logRecord);
    }

    public static function cancelOrder($order, $logRecord)
    {
        $logRecord->stage_id = Stage::getCanceledStageId();
        return self::updateStageInOrder($order, $logRecord);
    }
    
    public static function updateDeliveryPriceInOrder($orderId)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $order = Order::findOne(['id' => $orderId]);
            if ($order === null || $order->city_id === null) {
                $transaction->rollBack();
                return false;
            }
            
            $order->update_date = OrdersApi::getCurrentTimestamp();
           
            $hasDeliveryInfo = $order->deliveryInfo !== null;
            if (!$hasDeliveryInfo) {
                $deliveryInfo = new DeliveryInfo();
                $deliveryInfo->order_id = $order->id;
            } else {
                $deliveryInfo = $order->deliveryInfo;
            }
            
            self::setPriceInDeliveryInfo($deliveryInfo, $order);
            
            if (!$deliveryInfo->save()) {
                $transaction->rollBack();
                return false;
            }
           
            if (!$order->save()) {
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
    
    public static function updateDeliveryDateTimeInOrder($orderId)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $order = Order::findOne(['id' => $orderId]);
            if ($order === null || $order->city_id === null) {
                $transaction->rollBack();
                return false;
            }
            
            $order->update_date = OrdersApi::getCurrentTimestamp();
           
            $hasDeliveryInfo = $order->deliveryInfo !== null;
            if (!$hasDeliveryInfo) {
                $deliveryInfo = new DeliveryInfo();
                $deliveryInfo->order_id = $order->id;
            } else {
                $deliveryInfo = $order->deliveryInfo;
            }
            
            if ($order->is_postponed && !$hasDeliveryInfo) {
                self::copyDateTimeToDeliveryInfoFromOrder($deliveryInfo, $order);
            } elseif (!$order->is_postponed) {
                self::setDateTimeInDeliveryInfoAndOrder($deliveryInfo, $order);
            } else {
                // Заказ отложенный и для него уже была создана информация о доставке.
                $transaction->rollBack();
                return false;
            }
            
            if (!$deliveryInfo->save()) {
                $transaction->rollBack();
                return false;
            }
           
            if (!$order->save()) {
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
    
   
    public static function updateOrder($order) 
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $order->update_date = OrdersApi::getCurrentTimestamp();
            
            $oldAddress = Order::find()
                ->select('address')
                ->where(['id' => $order->id])
                ->scalar();
            
            if ($order->address !== $oldAddress) {
                // Адрес изменился и не соответствует тому что храниться в json.
                // Чтобы не путать пользователя обнулим значение в json.
                $order->address_json = null;
            }
            
            if (!$order->save()) {
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
    
    public static function createNewOrderViaApi($order, $newOrderItems = null)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
           
            
            if ($order->stage_id === null) {
                $order->stage_id = Stage::getNewStageId();
            }
            $stageId = $order->stage_id;
            
            if ($order->order_num === null) {
                $order->order_num = OrdersApi::getOrderNum();
            }
            if ($order->start_date === null) {
                $order->start_date = $currentTime; 
            }
            if ($order->update_date === null) {
                $order->update_date = $currentTime;
            }
            if ($order->is_postponed === null) {
                $order->is_postponed = false;
            }
            if ($order->client_id === null) {
                if (!empty($order->phone)) {
                    $order->client_id = OrdersApi::getClientIdByPhone($order->phone);
                }
            }

            // Если нужно заполним информацию о дате доставки.
            $hasDeliveryDateTime = 
                $order->delivery_date !== null && 
                $order->delivery_time !== null;

            $deliveryInfo = null;
            if ($hasDeliveryDateTime) {
                $deliveryInfo = new DeliveryInfo();
                self::copyDateTimeToDeliveryInfoFromOrder($deliveryInfo, $order);
            }
            
            $needAddOrderItems = $newOrderItems !== null;
            
            if ($needAddOrderItems) {
                // Продукты будут нужны чтобы заполнить OrderItems и 
                // создать записи о состоянии блюда.
                $newProducts = self::getProductsOnOrderItems($newOrderItems);
            }
            
            if ($needAddOrderItems) {
                self::fillNewOrderItems($newOrderItems, $newProducts, $order);
            }
            self::fillCartValuesInNewOrder($order, $newOrderItems);
            
            $logRecord = new LogRecord();
            $logRecord->stage_id = $stageId;
            $logRecord->date = $currentTime;
            
            // Сохраним все изменения.
            if (!$order->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if ($needAddOrderItems) {     
                foreach ($newOrderItems as $newOrderItem) {
                    // Добавим блюдо.
                    $newOrderItem->order_id = $order->id;
                    if (!$newOrderItem->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                    
                    // Добавим запись о состоянии блюда.
                    $newProduct = $newProducts[$newOrderItem->product_id];
                    $orderItemLog = new OrderItemLog();
                    self::fillNewOrderItemLog($orderItemLog, $newProduct);
                    $orderItemLog->order_id = $order->id;
                    if (!$orderItemLog->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
            
            if ($deliveryInfo !== null) {
                $deliveryInfo->order_id = $order->id;
                if (!$deliveryInfo->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            $logRecord->order_id = $order->id;
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
       
    public static function updateOrderViaApi($order, $orderValues, $newOrderItems = null)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            $previousStageId = (int) Order::find()
                ->select('stage_id')
                ->where(['id' => $order->id])
                ->scalar();
        
            $possibleAttributes = array_diff($order->attributes(), ['id']);
            foreach ($possibleAttributes as $attributeName) {
                if (isset($orderValues[$attributeName])) {
                    $order->$attributeName = $orderValues[$attributeName];
                }
            }
          
            if (!isset($orderValues['update_date'])) {
                $order->update_date = $currentTime;
            }
            
            $needUpdateOrderItems = $newOrderItems !== null;
            if ($needUpdateOrderItems) {
                // Сначала удалим старые элементы.
                $oldOrderItems = $order->orderItems;
                foreach ($oldOrderItems as $oldOrderItem) {
                    if ($oldOrderItem->delete() === false) {
                        $transaction->rollBack();
                        return false;
                    }
                }
                $oldOrderItemLogs = $order->orderItemLogs;
                foreach ($oldOrderItemLogs as $oldOrderItemLog) {
                    if ($oldOrderItemLog->delete() === false) {
                        $transaction->rollBack();
                        return false;
                    } 
                }
                
                // Продукты понадобяться для заполнения $newOrderItems и 
                // $newOrderItemLogs.
                $newProducts = self::getProductsOnOrderItems($newOrderItems);
 
                self::fillNewOrderItems($newOrderItems, $newProducts, $order);
                foreach ($newOrderItems as $newOrderItem)  {
                    $newOrderItem->order_id = $order->id;
                }
                
                $newOrderItemLogs = [];
                foreach ($newProducts as $newProduct) {
                    $newOrderItemLog = new OrderItemLog();
                    self::fillNewOrderItemLog($newOrderItemLog, $newProduct);
                    $newOrderItemLog->order_id = $order->id;
                    $newOrderItemLogs []= $newOrderItemLog;
                }
 
                if (!isset($orderValues['items_count'])) {
                    $order->items_count = self::getOrderItemsCount($newOrderItems, $order);   
                }
                if (!isset($orderValues['total_price'])) {
                    $order->total_price = self::getOrderItmesTotalPrice($newOrderItems, $order);
                }
                if ($order->tax === null) {
                    $order->tax = 0;
                }
                if (!isset($orderValues['total_pay'])) {
                    self::setTotalPayInOrder($order);
                }      
            }

            // Если нужно заполним информацию о дате и цене доставки.
            $hasDeliveryInfo = $order->deliveryInfo !== null;
            $needUpdateDeliveryPrice = $needUpdateOrderItems;
            $needUpdateDeliveryDateTime = 
                isset($orderValues['delivery_date']) && 
                isset($orderValues['delivery_time']);
            $needUpdateDeliveryInfo = $needUpdateDeliveryDateTime || $needUpdateDeliveryPrice;
            
            $deliveryInfo = null;
            if ($needUpdateDeliveryInfo) {
                if (!$hasDeliveryInfo) {
                    $deliveryInfo = new DeliveryInfo();
                    $deliveryInfo->order_id = $order->id;
                } else {
                    $deliveryInfo = $order->deliveryInfo;
                }
            }
            if ($needUpdateDeliveryPrice) {
                self::setPriceInDeliveryInfo($deliveryInfo, $order);
            }
            if ($needUpdateDeliveryDateTime) {                          
                self::copyDateTimeToDeliveryInfoFromOrder($deliveryInfo, $order);
            }
            
            $logRecord = null;
            if (isset($orderValues['stage_id'])) {
                $newStageId = (int) $orderValues['stage_id'];
                if ($newStageId !== $previousStageId) { 
                    // Если меняется стадия заказа добавим запись в лог.
                    $logRecord = new LogRecord();
                    $logRecord->order_id = $order->id;
                    $logRecord->stage_id = $newStageId;
                    $logRecord->date = $currentTime;                    
                }
            }   
            
            // Сохраним все изменения.
            if (!$order->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if ($needUpdateOrderItems) {
                foreach ($newOrderItems as $newOrderItem) {
                    if (!$newOrderItem->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
                foreach ($newOrderItemLogs as $newOrderItemLog) {
                    if (!$newOrderItemLog->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
            
            if ($needUpdateDeliveryInfo) {
                if (!$deliveryInfo->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if ($logRecord !== null) {
                if (!$logRecord->save()) {
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
    
    public static function updateStageInOrderViaApi($order, $logRecord)
    {
       return self::updateStageInOrder($order, $logRecord);
    }
    
    public static function updateStageInOrder($order, $logRecord)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $currentTime = OrdersApi::getCurrentTimestamp();
            $stageId = (int) $logRecord->stage_id;
            
            $logRecord->order_id = $order->id;
            $logRecord->date = $currentTime;

            $order->update_date = $currentTime;
            $order->stage_id = $stageId;
            $order->is_deleted = false;
            if ($stageId === Stage::getCanceledStageId()) {
                $order->is_deleted = true;
            }
            /*
            //Нужно ли это?
            if (
                $stageId === Stage::getCanceledStageId() ||
                $stageId === Stage::getDeliveredStageId()
            ) {
                $order->end_date = $currentTime; 
            }
            */
            if (!$logRecord->save()) {
                $transaction->rollBack();
                return false;
            }
            if (!$order->save()) {
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
    
    private static function setCartValuesInOrder($order)
    {
        $itemsCount = (int) OrderItem::find()
                ->where(['order_id' => $order->id])
                ->count();
        $totalPrice = (int) OrderItem::find()
            ->select(new Expression("SUM(total_price)"))
            ->where(['order_id' => $order->id])
            ->scalar();
            
        $order->items_count = $itemsCount;
        $order->total_price = $totalPrice;
        self::setTotalPayInOrder($order);
    }
    
    /**
     * Перед вызовом этой функции total_price и tax в заказе уже дожны 
     * иметь верные значения.
     * @param \app\modules\orders\models\Order $order
     */
    private static function setTotalPayInOrder($order)
    {
        $order->total_pay = $order->total_price - $order->tax;
    }
    
    
    private static function setPriceInDeliveryInfo($deliveryInfo, $order)
    {

        $cityId = $order->city_id;
        $minTotalPriceForFreeDelivery = OptionVal::getMinTotalPriceForFreeDelivery($cityId);
        if ($minTotalPriceForFreeDelivery === null) {
            throw new \RuntimeException('Параметр не задан.');            
        }
        if ($order->total_price <= (int) $minTotalPriceForFreeDelivery) {
            $deliveryInfo->price = 0;
        } else {
            $deliveryInfo->price = (int) OptionVal::getDeliveryCostValue($cityId);
        }
    }
    
    private static function setDateTimeInDeliveryInfoAndOrder($deliveryInfo, $order) 
    {
        $cityId = $order->city_id;
        $currentTime = OrdersApi::getCurrentTimestamp();
        $deliveryTime = OptionVal::getDeliveryTimeValue($cityId);
        if ($deliveryTime === null) {
            throw new \RuntimeException('Параметр не задан.');
        }
        
        $intrval = new \DateInterval('PT'.$deliveryTime.'M');
        $deliveryDatetime = (new \DateTime())->setTimestamp($currentTime)->add($intrval);
        
        $deliveryInfo->planned_delivery_date = $deliveryDatetime->format(DeliveryInfo::DATE_FORMAT);
        $deliveryInfo->planned_delivery_time = $deliveryDatetime->format(DeliveryInfo::TIME_SHORT_FORMAT);
        $order->delivery_date = $deliveryDatetime->format(Order::DATE_FORMAT);
        $order->delivery_time = $deliveryDatetime->format(Order::TIME_SHORT_FORMAT);
    }
    
    private static function copyDateTimeToDeliveryInfoFromOrder($deliveryInfo, $order)
    {
        
        $deliveryInfo->planned_delivery_date = DateHelper::convertDate(
            $order->delivery_date, 
            Order::DATE_FORMAT, 
            DeliveryInfo::DATE_FORMAT
        );
        $deliveryInfo->planned_delivery_time = TimeHelper::convertTime(
            $order->delivery_time, 
            Order::TIME_SHORT_FORMAT, 
            DeliveryInfo::TIME_SHORT_FORMAT
        );
    }
    
    private static function fillNewOrderItems($newOrderItems, $products, $order)
    {        
        foreach ($newOrderItems as $newOrderItem) {
            if ($newOrderItem->price === null) {
                if (!isset($products[$newOrderItem->product_id])) {
                    throw new \Exception('Продукт не найден.');
                }
                $newOrderItem->price = $products[$newOrderItem->product_id]->price;
            }
            if ($newOrderItem->total_price === null) {
                self::setTotalPriceInOrderItem($newOrderItem, $order);
            }  
        }
    }

    // Перед вызовом функции поле total_price у элементов $newOrderItems должно 
    // быть заполнено, если, конечно, $newOrderItems !== null.
    private static function fillCartValuesInNewOrder($order, $newOrderItems = null)
    {  
        $itemsCount = 0;
        if ($newOrderItems !== null) {
            $itemsCount = self::getOrderItemsCount($newOrderItems, $order);
        }
        
        $totalPrice = 0;
        if ($newOrderItems !== null) {
           $totalPrice = self::getOrderItmesTotalPrice($newOrderItems, $order);
        }
        
        if ($order->items_count === null) {
            $order->items_count = $itemsCount;    
        }
        if ($order->total_price === null) {
            $order->total_price = $totalPrice;
        }
        if ($order->tax === null) {
            $order->tax = 0;
        }
        if ($order->total_pay === null) {
            self::setTotalPayInOrder($order);
        }       
    }
    
    private static function getOrderItmesTotalPrice($orderItems, $order)
    {
        $totalPrice = 0;
        if ($orderItems !== null) {
            foreach ($orderItems as $orderItem) {
                $totalPrice += $orderItem->total_price;
            }
        }
        return $totalPrice;
    }
    
    private static function getOrderItemsCount($orderItems, $order) {
        $itemsCount = 0;
        if ($orderItems !== null) {
            $itemsCount = count($orderItems);   
        }
        return $itemsCount;
    }
    
    private static function setTotalPriceInOrderItem($orderItem, $order)
    {
        $orderItem->total_price = (int) $orderItem->quantity * (int) $orderItem->price; 
    }
        
    private static function getProductsOnOrderItems($orderItems)
    {
        $productIds = [];
        foreach ($orderItems as $orderItem) {
            $productIds []= $orderItem->product_id;
        }
        $products = OrdersApi::getProductsByIds($productIds);
        
        return ArrayHelper::index($products, 'id');
    }
    
    private static function fillNewOrderItemLog($newOrderItemLog, $product)
    {
        $newOrderItemLog->product_id = $product->id;
        $newOrderItemLog->state = OrderItemLog::STATE_NEW;
        $newOrderItemLog->station = $product->station;
    }

}
