<?php

namespace app\modules\clients\controllers;

use Yii;

use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\clients\models\Client;
use app\modules\clients\models\Address;
use app\modules\clients\models\ClientAddress;
use app\modules\clients\models\Bonuscard;


/**
 * Действиям этого контроллера нужно передать в GET параметр hash. 
 * Он формируется следющим образом. Берутся значения отправляемых 
 * параметров, упорядочиваются  в алфавитном порядке в соответсвии с 
 * их именами, последним параметром будет секретное слово(apiToken). 
 * Затем значения соединяются в строку с двоеточнием в качестве разделителя.
 * От полученный строки берётся md5-хэш. 
 */
class ClientsApiController extends DefaultController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login-auth' => ['get'],
                    'phone-auth' => ['get'],
                    'email-auth' => ['get'],
                    'client-get' => ['get'],
                    'client-update' => ['get'],
                    'addresses-get' => ['get'],
                    'bonuscard-get' => ['get'],
                    'bonuscard-update' => ['get'],
                    'address-add' => ['get'],
                    'address-search' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @param string $loign 
     * @param string $password
     * @return boolean|string Клиент в json.
     */
    public function actionLoginAuth()
    {        
        $this->checkAccess(['login', 'password']);
        $get = Yii::$app->request->queryParams;
    
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $client = Client::findOne(['login' => $get['login']]);
        if ($client === null) {
            return false;
        }
        if (!$client->validatePassword($get['password'])) {
            return false;
        }
        
        return $client;
    }
    
    /**
     * @param string $phone 
     * @param string $password
     * @return boolean|string Клиент в json.
     */
    public function actionPhoneAuth()
    {
        $this->checkAccess(['phone', 'password']);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $client = Client::findOne(['phone' => $get['phone']]);
        if ($client === null) {
            return false;
        }
        if (!$client->validatePassword($get['password'])) {
            return false;
        }
        
        return $client;
    }
    
    /**
     * @param string $email 
     * @param string $password
     * @return boolean|string Клиент в json.
     */
    public function actionEmailAuth()
    {
        $this->checkAccess(['email', 'password']);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $client = Client::findOne(['email' => $get['email']]);
        if ($client === null) {
            return false;
        }
        if (!$client->validatePassword($get['password'])) {
            return false;
        }
        
        return $client;
    }
    
    /**
     * @param string $id id клиента 
     * @return null|string Клиент в json.
     */
    public function actionClientGet()
    {
        $this->checkAccess(['id']);
        $get = Yii::$app->request->queryParams;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $client = Client::findOne(['id' => $get['id']]);
        if ($client === null) {
            return null;
        }
        
        $attributes = $client->attributes();
        $attributes []= 'ordersCount';
        $result = new \stdClass();
        
        $forbiddenParams = ['id', 'password'];
        foreach ($attributes as $attribute)
        {
            if (in_array($attribute, $forbiddenParams)) {
                continue;
            }
            $result->$attribute = $client->$attribute;
        }
        
        return $result;
    }
    
    /**
    * @param integer $id id клиента
    * @param string $name Необязательный.
    * @param string $fullname Необязательный.
    * @param string $birthday Необязательный.
    * @param string $login Необязательный.
    * @param string $email Необязательный.
    * @param string $password Необязательный.
    * @param string $phone Необязательный.
    * @param string $alterPhone Необязательный.
    * @param string $description Необязательный.
    * @param string $note Необязательный.
    * @param integer $cardnum Необязательный.
    * @param integer $state Необязательный.
    * 
    * @return boolean Успешна ли операция.
    */
    public function actionClientUpdate()
    {
        $get = Yii::$app->request->queryParams;
        if (!isset($get['id'])) {
            throw new ForbiddenHttpException();
        }
        
        $client = Client::findOne(['id' => $get['id']]);
       
        $attributes = $client->attributes();
        $possibleParams = array_diff($attributes, ['id']);
        $this->checkAccess(['id'], $possibleParams);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($client === null) {
            return false;
        }
        
        foreach ($possibleParams as $paramName) {
            if (isset($get[$paramName])) {
                $client->$paramName = $get[$paramName];
            }
        }
        return $client->save();
    }
    
    /**
     * @param string $id id клиента
     * @return boolean|string Адреса в json.
     */
    public function actionAddressesGet()
    {
        $this->checkAccess(['id']);
        
        $get = Yii::$app->request->queryParams;
        $clientAddresses = ClientAddress::find()
            ->with(['address'])
            ->where(['clientId' => $get['id']])
            ->all();
        
        $result = [];
        foreach ($clientAddresses as $clientAddress) {
            $addressAttributes = $clientAddress->address->attributes();
            $ordersCountAttribute = 'ordersCount';
            
            $address = new \stdClass();
            foreach($addressAttributes as $attribute) {
                $address->$attribute = $clientAddress->address->$attribute;     
            }
            $address->$ordersCountAttribute = $clientAddress->ordercount;
            $result []= $address;
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    /**
     * @param string $id id Клиента
     * @return null|string Бонусная карта в json.
     */
    public function actionBonuscardGet()
    {
        $this->checkAccess(['id']);
        
        $get = Yii::$app->request->queryParams;
        $client = Client::findOne(['id' => $get['id']]);
               
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($client === null) {
            return false;
        }
        
        return $client->bonuscard;
    }
    
    /**
     * @param string $id id Клиента
     * @param integer $type Необязательный.
     * @param integer $moneyquan Необязательный.
     * @param integer $bonuses Необязательный.
     * @return boolean Бонусная карта в json.
     */
    public function actionBonuscardUpdate()
    {
        $possibleAttributes = [
            'type',
            'moneyquan',
            'bonuses'
        ];
        $this->checkAccess(['id'], $possibleAttributes);
        
        $get = Yii::$app->request->queryParams;
        $client = Client::findOne(['id' => $get['id']]);
        
        $isBonuscardExist = $client->bonuscard !== null;
        
        if (!$isBonuscardExist) {
            $bonuscard = new Bonuscard();
        } else {
            $bonuscard = $client->bonuscard;
        }

        foreach ($possibleAttributes as $attribute) {
            if (isset($get[$attribute])) {
                $bonuscard->$attribute = $get[$attribute]; 
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($isBonuscardExist) {
            return $bonuscard->save();
        } else {
            return Bonuscard::createBonuscard($client, $bonuscard);
        }
    }
    
    /**
     * @param integer $clientId Необязательный.
     * @param integer $cityId
     * @param string $street
     * @param string $home
     * @param string $appart
     * @param integer $floor Необязательный.
     * @param string $code Необязательный.
     * @param string $entrance Необязательный.
     * @param string $name Необязательный.
     * @param string $desc Необязательный.
     * @return boolean Успешна опреация или нет.
     */
    public function actionAddressAdd()
    {
        $mandatoryAttributes = [
            'cityId',
            'street',
            'home',
            'appart',
        ];
        $possibleAttributes = [
            'clientId',
            'code',
            'entrance',
            'floor',
            'name',
            'desc',
        ];
        
        $this->checkAccess($mandatoryAttributes, $possibleAttributes);
        
        $get = Yii::$app->request->queryParams;
     
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return Address::addAddress($get); 
    }
    
    /**
     * @param integer $cityId Необязательный.
     * @param string $street Необязательный.
     * @param string $home Необязательный.
     * @param string $appart Необязательный.
     * @param string $name Необязательный.
     * @return string Адреса в json.
     */
    public function actionAddressSearch()
    {
        $possibleParams = [
            'cityId' => null,
            'street' => null,
            'home' => null,
            'appart' => null,
            'name' => null,
        ];
        
        $possibleAttributes = array_keys($possibleParams);
        $this->checkAccess([], $possibleAttributes);
      
        $get = Yii::$app->request->queryParams;
        $params = ArrayHelper::merge($possibleParams, $get);
        
        $addresses = Address::find()
            ->andFilterWhere([
                'cityId' => $params['cityId'],
                'street' => $params['street'],
                'home' => $params['home'],
                'appart' => $params['appart'],
            ])
            ->andFilterWhere(['like', 'name', $params['name']])
            ->all();
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $addresses;
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
            
            $params[$key] = $get[$key];
        }
        foreach ($additionalKeys as $key) {
            if (!isset($get[$key])) {
                continue;
            }
            
            $params[$key] = $get[$key];
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

}
