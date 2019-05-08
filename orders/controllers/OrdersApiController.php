<?php

namespace app\modules\orders\controllers;

use Yii;

use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\modules\orders\models\Order;
use app\modules\orders\models\OrderItem;
use app\modules\orders\models\LogRecord;
use app\modules\orders\models\OptionVal;
use app\modules\orders\models\search\OrderApiSearch;

/**
 * Действиям этого контроллера нужно передать в GET параметр hash. 
 * Он формируется следющим образом. Берутся значения отправляемых 
 * параметров, упорядочиваются  в алфавитном порядке в соответсвии с 
 * их именами, последним параметром будет секретное слово(apiToken).
 * Затем значения соединяются в строку с двоеточнием в качестве разделителя. 
 * От полученный строки берётся md5-хэш.
 */
class OrdersApiController extends DefaultController
{
    const ORDER_NOT_FOUND_ERROR_MESSAGE = 'Заказ не найден.';
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'order-create' => ['get'],
                    'order-update' => ['get'],
                    'stage-update' => ['get'],
                    'order-get-by-id' => ['get'],
                    'order-get-by-order-num' => ['get'],
                    'orders-get' => ['get'],
                ],
            ],
        ];
    }
    
    /**
    * @param  string $phone Обязательный.
    * @param  string $order_num Необязательный.
    * @param  integer $user_id Необязательный.
    * @param  integer $client_id Необязательный.
    * @param  string $recipient Необязательный.
    * @param  string $alter_phone Необязательный.
    * @param  integer $city_id Необязательный.
    * @param  string $address Необязательный.
    * @param  string address_json Необязательный.
    * @param  integer $start_date Необязательный.  
    * @param  integer $update_date Необязательный.
    * @params integer $end_date Необязательный.
    * @param  integer $person_num Необязательный.
    * @param  integer $items_count Необязательный.
    * @param  integer $total_price Необязательный.
    * @param  integer $tax Необязательный.
    * @param  integer $total_pay Необязательный.
    * @param  integer $is_paid Необязательный.
    * @param  integer $is_deleted Необязательный.
    * @param  integer $payment_type Необязательный.
    * @param  integer $return_sum Необязательный.
    * @param  integer $is_postponed Необязательный.
    * @param  string $delivery_date Дата в формате d-m-Y. Например 01-08-2016. Необязательный. 
    * @param  string $delivery_time Дата в формате H:i Например 11:30. Необязательный.
    *    Если передаётся delivery_date, то delivery_time также обязательно 
    *    должно передаваться.
    * @param  string $comment
    * @param string $orderItems Закодированная в json информация о составе заказа.
    * @return string Ответ успешна ли операция в json, с id заказа успешна или
    *   errorMessage в случае ошибки.
    */    
    public function actionOrderCreate()
    {        
        $orderAttributes = $this->getOrderAttributes();
        
        // Может быть передано всё кроме id.
        $possibleOrderAttributes = array_diff($orderAttributes, ['id']);
        
        // Телофон будет среди обязательных параметров, поэтому уберём его.
        $possibleParamNames = array_diff($possibleOrderAttributes, ['phone']);
        $possibleParamNames = ArrayHelper::merge($possibleParamNames, ['orderItems']);  

        $this->checkAccess(['phone'], $possibleParamNames);
        $get = Yii::$app->request->queryParams;
    
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $newOrder = new Order();
        foreach ($possibleOrderAttributes as $attributeName) {
            if (isset($get[$attributeName])) {
                $newOrder->$attributeName = $get[$attributeName];  
            }
        }
        $newOrderItems = null;
        if (isset($get['orderItems'])) {
            $newOrderItems = $this->prepareNewOrderItems($get);
        }
        
        $success = Order::createNewOrderViaApi($newOrder, $newOrderItems);
        
        if (!$success) {
            $this->getErrorObjectOnModel($newOrder);
        }
        
        $result = new \stdClass();
        $result->succes = true;
        $result->id = $newOrder->id;
        
        return $result;
    }
    
    /**
    * @param  integer $id Обязательный.
    * @param  string $order_num Необязательный.
    * @param  integer $user_id Необязательный.
    * @param  integer $client_id Необязательный.
    * @param  string $recipient Необязательный.
    * @param  string $phone Необязательный.
    * @param  string $alter_phone Необязательный.
    * @param  integer $city_id Необязательный.
    * @param  string $address Необязательный.
    * @param  string address_json Необязательный.
    * @param  integer $start_date Необязательный.  
    * @param  integer $update_date Необязательный.
    * @params integer $end_date Необязательный.
    * @param  integer $person_num Необязательный.
    * @param  integer $items_count Необязательный.
    * @param  integer $total_price Необязательный.
    * @param  integer $tax Необязательный.
    * @param  integer $total_pay Необязательный.
    * @param  integer $is_paid Необязательный.
    * @param  integer $is_deleted Необязательный.
    * @param  integer $payment_type Необязательный.
    * @param  integer $return_sum Необязательный.
    * @param  integer $is_postponed Необязательный.
    * @param  string $delivery_date Дата в формате d-m-Y. Например 01-08-2016. Необязательный.
    * @param  string $delivery_time Дата в формате H:i Например 11:30. Необязательный.
    *    Если передаётся delivery_date, то delivery_time также обязательно 
    *    должно передаваться.
    * @param  string $comment
    * @param string $orderItems Закодированная в json информация о составе заказа.
    * @return string Ответ успешна ли операция в json, с id заказа успешна или
    *   errorMessage в случае ошибки.
    */ 
    public function actionOrderUpdate()
    {
        $orderAttributes = $this->getOrderAttributes();
        
        // Может быть передано всё кроме id.
        $possibleOrderAttributes = $orderAttributes;
        
        // id будет среди обязательных параметров, поэтому уберём его.
        $possibleParamNames = array_diff($possibleOrderAttributes, ['id']);
        $possibleParamNames = ArrayHelper::merge($possibleParamNames, ['orderItems']);  

        $this->checkAccess(['id'], $possibleParamNames);
        $get = Yii::$app->request->queryParams;
    
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $order = $this->findOrderModel($get['id']);
        if ($order === null) {
            return $this->getErrorObject(self::ORDER_NOT_FOUND_ERROR_MESSAGE);
        }
        
        $newOrderItems = null;
        if (isset($get['orderItems'])) {
            $newOrderItems = $this->prepareNewOrderItems($get);
        }
        
        $success = Order::updateOrderViaApi($order, $get, $newOrderItems);
        
        if (!$success) {
            $this->getErrorObjectOnModel($order);
        }
        
        $result = new \stdClass();
        $result->succes = true;
        
        return $result;
    }
    
    /**
    * @param integer $id ID заказа. Обязательный.
    * @param integer $stage_id Статус. Обязательный.
    * @param string $comment Необязательный.
    * @return string Ответ успешна ли операция в json.
    */  
    public function actionStageUpdate()
    {
        $this->checkAccess(['id', 'stage_id'], ['comment']);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $order = $this->findOrderModel($get['id']);
        if ($order === null) {
            return $this->getErrorObject(self::ORDER_NOT_FOUND_ERROR_MESSAGE);
        }
        
        $logRecord = new LogRecord();
        $logRecord->stage_id = (int) $get['stage_id'];
        if (isset($get['comment'])) {
            $logRecord->comment = $get['comment'];
        }
        $success = Order::updateStageInOrderViaApi($order, $logRecord);
        if (!$success) {
            return $this->getErrorObjectOnModel($logRecord);
        }
        
        $result = new \stdClass();
        $result->succes = true;
        return $result;   
    }
        
    /**
    * @param integer $id ID заказа. Обязательный.
    * @return string Заказ в json или сообщение об ошибке есди заказ найти не удалось.
    */  
    public function actionOrderGetById()
    {
        $this->checkAccess(['id']);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
                
        $order = $this->findOrderModel($get['id']);
        if ($order === null) {
            return $this->getErrorObject(self::ORDER_NOT_FOUND_ERROR_MESSAGE);
        }
        
        $preparedOrder = $this->prepareOrderToJsonEncode($order);

        return $preparedOrder;                 
        /*
        $result = new \stdClass();
        $result->succes = true;
        $result->order = $preparedOrder;
        return $result;
        */
    }
    
    /**
    * @param string $order_num Номер заказа. Обязательный.
    * @return string Заказ в json или сообщение об ошибке есди заказ найти не удалось.
    */  
    public function actionOrderGetByOrderNum()
    {
        $this->checkAccess(['order_num']);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
                
        $order = Order::findOne(['order_num' => $get['order_num']]);
        if ($order === null) {
            return $this->getErrorObject(self::ORDER_NOT_FOUND_ERROR_MESSAGE);
        }
        
        $preparedOrder = $this->prepareOrderToJsonEncode($order);

        return $preparedOrder;                 
    }
    
    /** 
    * @param count Обязательный.
    * @param id Необязательный.
    * @param phone Необязательный.
    * @param order_num Необязательный.
    * @param stage_id Необязательный.
    * @param integer city_id  Необязательный.
    * @param integer $dateStartFrom Необязательный.
    * @param integer $dateStartTo Необязательный.
    * @return string Заказы в json или пустой массив если заказов не найдено. 
    */
    public function actionOrdersGet()
    {
        $this->checkAccess(['count'], [
            'id',
            'phone',
            'order_num',
            'stage_id',
            'city_id',
            'dateStartFrom',
            'dateStartTo',
        ]);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $orderApiSearch = new OrderApiSearch();
        $orders = $orderApiSearch->search($get);
        $preparedOrders = $this->prepareOrdersToJsonEncode($orders);

        return $preparedOrders;                 
    }
    
    private function prepareOrdersToJsonEncode($orders)
    {
        $result = [];
        foreach ($orders as $order) {
            $result []= $this->prepareOrderToJsonEncode($order);
        }
        return $result;
    }
    
    private function prepareOrderToJsonEncode($order)
    {
        $result = new \stdClass();
        $orderAttributes = $order->attributes();
        foreach ($orderAttributes as $attributeName) {
            $result->$attributeName =  $order->$attributeName;
        }
        
        
        if (count($order->orderItems) > 0) {
            $result->orderItems = $order->orderItems;
        }
        if ($order->deliveryInfo !== null) {
            $result->deliveryInfo = $order->deliveryInfo;
        }
        if (count($order->logRecords) > 0) {
            $result->logRecords = $order->logRecords; 
        }
        return $result;
    }
    
    private function getErrorObjectOnModel($model)
    {
        $errors = $model->getFirstErrors();
        if (count($errors) === 0) {
            $errorMessage = null;
        } else {
            $errorMessage = $errors[0];
        }
        return $this->getErrorObject($errorMessage);
    }
    
    private function getErrorObject($errorMessage)
    {
        $result = new \stdClass();
        $result->success = false;
        if (!empty($errorMessage)) {
            $result->errorMessage = $errorMessage;
        }
        
        return $result;
    }
    
    private function prepareNewOrderItems($get)
    {
        $orderItemAttributes = $this->getOrderItemAttributes();
        $possibleOrderItemParamNames = array_diff($orderItemAttributes, ['id', 'order_id']);
        $newOrderItems = [];
        $orderItems = Json::decode($get['orderItems'], false);
            
        foreach ($orderItems as $orderItem) {
            $newOrderItem = new OrderItem();
            foreach ($possibleOrderItemParamNames as $paramName) {
                if (isset($orderItem->$paramName)) {
                    $newOrderItem->$paramName = $orderItem->$paramName;
                }
            }
            $newOrderItems []= $newOrderItem;
        }
        
        return $newOrderItems;
    }

    private function checkAccess($mandatoryKeys, $additionalKeys = []) 
    {
        $get = Yii::$app->request->queryParams;
        if (!isset($get['hash'])) {
            throw new ForbiddenHttpException();  
        }

        if ($this->calcHash($mandatoryKeys, $additionalKeys) !== $get['hash']) {
            throw new ForbiddenHttpException();  
        }
        
        return true;
    }
    
    private function calcHash($mandatoryKeys, $additionalKeys = [])
    {
        $get = Yii::$app->request->queryParams;
        $params = [];
        foreach ($mandatoryKeys as $key) {
            if (!isset($get[$key])) {
                throw new ForbiddenHttpException();
            }
            
            $this->addParamToParams($get, $key, $params);
        }
        foreach ($additionalKeys as $key) {
            if (!isset($get[$key])) {
                continue;
            }
            
            $this->addParamToParams($get, $key, $params);
        }
       
        ksort($params);
        $arr = [];
        foreach ($params as $paramValue) {
            $arr []= $paramValue;
        }
        
        $arr [] = $this->getApiToken();
        $str = implode(':', $arr);
        return md5($str);
    }
    
    private function addParamToParams($get, $key, &$params)  
    {
        if (is_array($get[$key])) {
            $arr = $get[$key];
            foreach ($arr as $index => $value) {
                $paramName = $key.'['.$index.']';
                $params[$paramName] = $value; 
            }
        } else {
            $params[$key] = $get[$key];
        }

        return $params;
    }
    
    private function getApiToken()
    {
        return OptionVal::getSecretKeyDefault();
    }

    private function getOrderAttributes()
    {
        return (new Order())->attributes();
    }
    
    private function getOrderItemAttributes()
    {
        return (new OrderItem())->attributes();
    }
    
    private function findOrderModel($id)
    {
        return Order::findOne(['id' => $id]);
    }

}
