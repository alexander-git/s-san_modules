<?php

namespace app\modules\orders\controllers;

use Yii;

use yii\helpers\Json;
use app\modules\orders\models\OptionVal;
use app\modules\orders\models\OrdersApi;

class TestApiController extends DefaultController
{
    
    public function actionTest()
    {
        echo OrdersApi::getOrderNum();
    }
    
    public function actionOrderCreate()
    {   
        $baseUrl = 'orders/orders-api/order-create';
        
        $products = [
            '289' => 3,
            '287' => 1,
            '286' => 1,
            
        ];
        $orderItems = [];
        foreach ($products as $productId => $quantity)
        {
            $orderItem = new \stdClass();
            $orderItem->product_id = $productId;
            $orderItem->quantity = $quantity;
            $orderItems []= $orderItem;
        }
        
        $params = [
            'phone' => '81111111111',
            'city_id' => 2,
            'orderItems' => Json::encode($orderItems),
        ];
        $this->addHashToParams($params);
        echo $this->postGetRequest($baseUrl, $params);
    }

    public function actionOrderUpdate()
    {   
        $baseUrl = 'orders/orders-api/order-update';
        
        $products = [
            '289' => 3,
            '287' => 1,
            '286' => 1,
            
        ];
        $orderItems = [];
        foreach ($products as $productId => $quantity)
        {
            $orderItem = new \stdClass();
            $orderItem->product_id = $productId;
            $orderItem->quantity = $quantity;
            $orderItems []= $orderItem;
        }
        
        $params = [
            'delivery_date' => '20-08-2016',
            'delivery_time' => '11:30',
            'id' => 1,
            'stage_id' => 4,
            'comment' => 'Тестовый комментарий',
            'orderItems' => Json::encode($orderItems),
        ];
        $this->addHashToParams($params);
        echo $this->postGetRequest($baseUrl, $params);
    }
    
    
    public function actionStageUpdate()
    {   
        $baseUrl = 'orders/orders-api/stage-update';
        
        $params = [
            'id' => 1,
            'stage_id' => 3,
            'comment' => 'Тестовый комментарий',
        ];
        $this->addHashToParams($params);
        echo $this->postGetRequest($baseUrl, $params);
    }

    
    public function actionOrderGetById()
    {   
        $baseUrl = 'orders/orders-api/order-get-by-id';
        
        $params = [
            'id' => 1
        ];
        $this->addHashToParams($params);
        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionOrderGetByOrderNum()
    {   
        $baseUrl = 'orders/orders-api/order-get-by-order-num';
        
        $params = [
            'order_num' => '1'
        ];
        $this->addHashToParams($params);
        echo $this->postGetRequest($baseUrl, $params);
    }
    
    
    public function actionOrdersGet()
    {   
        $baseUrl = 'orders/orders-api/orders-get';
        
        $params = [
            'count' => 3,
            'phone' => 	'81111111111',
        ];
        $this->addHashToParams($params);
        $json = $this->postGetRequest($baseUrl, $params);
        $this->outJsonResult($json);
    }
    
    private function outJsonResult($json)
    {
        echo '<pre>';
        echo print_r(Json::decode($json, false), true);
        echo '</pre>';
    }
    
    private function postGetRequest($baseUrl, $params)
    {
        $ch = curl_init(); 
        if ($ch === false) {
            throw new \Exception(curl_error($ch));
        }
        
        $urlValue = [$baseUrl];
        foreach ($params as $paramName => $paramValue) {
            $urlValue[$paramName] = $paramValue;
        }
        $requestUrl = Yii::$app->urlManager->createAbsoluteUrl($urlValue); 
        
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch); 
        curl_close($ch);
        
        return $result;
    }
    
    private function addHashToParams(&$params)
    {
        $hash = $this->calcHash($params);
        $params['hash'] = $hash;
        return $params;
    }
    
    private function calcHash($params)
    {  
        ksort($params);
        $arr = [];
        foreach ($params as $paramValue) {
            $arr []= $paramValue;
        }
                
        $arr [] = $this->getApiToken();
        $str = implode(':', $arr);
        return md5($str);
    }
       
    private function getApiToken()
    {
        return OptionVal::getSecretKeyDefault();
    }
    
}
