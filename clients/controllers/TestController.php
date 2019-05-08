<?php

namespace app\modules\clients\controllers;

use Yii;

class TestController extends DefaultController
{
    public function actionLoginAuth()
    {   
        $baseUrl = 'clients/clients-api/login-auth';
        $params = [
            'login' => 'ivan',
            'password' => '12345',
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionPhoneAuth()
    {   
        $baseUrl = 'clients/clients-api/phone-auth';
        $params = [
            'phone' => '12345678910',
            'password' => '1234',
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionEmailAuth()
    {   
        $baseUrl = 'clients/clients-api/email-auth';
        $params = [
            'email' => 'ivan@ivanov.ru',
            'password' => '1234',
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionClientGet()
    {
        $baseUrl = 'clients/clients-api/client-get';
        $params = [
            'id' => 10,
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionClientUpdate()
    {
        $baseUrl = 'clients/clients-api/client-update';
        $params = [
            'id' => 1,
            'name' => 'ivan1'
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionAddressesGet()
    {
        $baseUrl = 'clients/clients-api/addresses-get';
        $params = [
            'id' => 1,
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionBonuscardGet()
    {
        $baseUrl = 'clients/clients-api/bonuscard-get';
        $params = [
            'id' => 10,
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionBonuscardUpdate()
    {
        $baseUrl = 'clients/clients-api/bonuscard-update';
        $params = [
            'id' => 1,
            'type' => 2,
            'moneyquan' => 9999,
            'bonuses' => 1000,
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionAddressAdd()
    {
        $baseUrl = 'clients/clients-api/address-add';
        $params = [
            'cityId' => 1,
            'street' => 'Абрикосовая',
            'home' => 3,
            'appart' => 11,
            'clientId' => 1,
        ];
        $this->addHashToParams($params);

        echo $this->postGetRequest($baseUrl, $params);
    }
    
    public function actionAddressSearch()
    {
        $baseUrl = 'clients/clients-api/address-search';
        $params = [
            'street' => 'Абрикосовая',
        ];
        $this->addHashToParams($params);
        echo $this->postGetRequest($baseUrl, $params);
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
 
}
