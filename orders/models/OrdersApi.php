<?php

namespace app\modules\orders\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\User;
use app\modules\clients\models\Client;

/**
* Все функции которые возврщают продукты(или продукт) должны возвращать их 
* в виде объектов \stdClass т.к. в процессе обработки к этому объекту будут 
* добавлены новые свойства. Изначально должны быть свойства id, 
* main_category_id, parent_id, option_generate, name, slug, price, station. 
*/
class OrdersApi
{
    private static $BASE_URL = 'http://sandotplant.host';
 
    private static $categoryStations = null;
    
    
    /**
     * @return integer
     */
    public static function getCurrentTimestamp() 
    {
        return time();
    }
 
    /**
     * @return integer|null
     */
    public static function getCurrentUserId() 
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        return Yii::$app->user->identity->id;
    }
    
    /**
     * @return string|null
     */
    public static function getUserNameById($id)
    {
        $user = User::findById($id);
        if ($user === null) {
            return null;
        }
        return $user->getShortName();
    }
    
    /**
     * @return string Название города.
     */
    public static function getCityNameById($cityId)
    {
        if ((int) $cityId === self::getDefaultCityId()) {
            return self::getDefaultCityName();
        }
        
        return self::getCitiesList()[(int) $cityId];
    }
 
    /**
     * @return integer
     */
    public static function getDefaultCityId()
    {
        return 0;
    }
    
    /**
     * @return string
     */
    public static function getDefaultCityName()
    {
        return 'По умолчанию';
    }
    
    /**
     * Должна возвращать уникальный номер заказа, который присваивается ему 
     * при создании.
     * @return string
     */
    public static function getOrderNum()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        
        $arr = [];
        for ($i = 1; $i <= 2; $i++) {
            $index = rand(0, strlen($alphabet) - 1);
            $arr []= $alphabet[$index];
        }
        for ($i = 1; $i <= 4; $i++) {
            $arr []= rand(0, 9);
        }
        shuffle($arr);
        return implode('', $arr);
    }

    
    /**
     * Возвращает список городов.
     * @return array
     */
    public static function getCitiesList() 
    {
        $url = self::$BASE_URL.'/orders/get-cities-list';
        $citiesJson = self::postGetRequest($url);
        $cities = Json::decode($citiesJson, false);
        return ArrayHelper::map($cities, 'id', 'name');
    }
    
    /**
     * Должна возвращать объект у котрого есть свойства 
     *  id, name, phone, alterPhone, state, stateName,
     * @param integer $id
     * @return null|\app\modules\clients\models\Client
     */
    public static function getClientById($id)
    {
        return Client::findOne(['id' => $id]);
    }
    
    /**
     * @param integer $id
     * @return string|null
     */
    public static function getClientNameById($id)
    {
        $client = Client::findOne(['id' => $id]);
        if ($client === null) {
            return null;
        }
        
        return $client->name;
    }
    
    /**
     * @param string $phone
     * @return integer|null
     */
    public static function getClientIdByPhone($phone)
    {
        $client = Client::findOne(['phone' => $phone]);
        if ($client === null) {
            return null;
        } else {
            return $client->id;
        }
    }
    
    /**
     * Возвращает бонусную карту. Должна возвращать объект у которого есть поля 
     * id, type, moneyquan, bonuses, typeName.
     * @param intgeer clinetId
     * @return \stdClass|null
     */
    public static function getBonuscardByClientId($clientId)
    {
        $client = Client::find()
            ->with(['bonuscard'])
            ->where(['id' => $clientId])
            ->one();
        
        if ($client === null) {
            return null;
        }
        $bonuscard = $client->bonuscard;
        if ($bonuscard === null) {
            return null;
        }

        $result = new \stdClass();
        $result->id = $bonuscard->id;
        $result->type = $bonuscard->type;
        $result->moneyquan = $bonuscard->moneyquan;
        $result->bonuses = $bonuscard->bonuses;
        $result->typeName = $bonuscard->bonuscardType->name;
        return $result;
    }
    
    /**
     * Снимает деньги равные величние скидки с бонусного счёта клиента.
     * Так как оператор может ошибиться при вводе, то эта опрерация может 
     * быть также и коррекцией предыдущих действий. Для того чтобы осуществить 
     * её правильно дополнительно передаётся значение скидки 
     * которое было списано с бонусного счета до этого.
     * @param integer $clientId
     * @param integer $tax
     * @param integer $previousTax
     * @return boolean
     */
    public static function setTaxFromBonuses($clientId, $tax, $previousTax = 0)
    {
        $client = Client::find()
            ->with(['bonuscard'])
            ->where(['id' => $clientId])
            ->one();
        if ($client === null) {
            return false;
        }
        $bonuscard = $client->bonuscard;
        if ($bonuscard === null) {
            return false;
        }
        return $bonuscard->updateCounters(['bonuses' => (-$tax + $previousTax)]);
    }
    
    /*
    // Промокоды.
    public static function sendPromoCode($code, $orderInfo)
    {
        $result = new \stdClass();
        $result->success = true;
        $resultJson = Json::encode($result);
        return $resultJson;
    } 
    */
    
    /**
     * @param string $cityName
     * @return array Массив в котором у объектов должны быть свойства id и name
     */
    public static function getCategoriesByCityName($cityName)
    {
        $url = self::$BASE_URL.'/orders/get-categories-by-city-name';
        $categoriesJson = self::postGetRequest($url, ['cityName' => $cityName]);
        $categories = Json::decode($categoriesJson, false);
        return $categories;
    }
    
    /**
     * @return stdClass Возвращает объект у которого есть два поля - properties
     * и values. В properties храниться массив объектов с возможными свойствами 
     * продуктов. Они должны иметь поля: id, property_group_id, name, key.
     * В values храниться массив объектов с возможными значениями свойств 
     * продуктов. Они должны иметь поля: id, property_id, name, value, slug.
     * Это нужно для формирования возможных комбинаций продуктов.
     */
    public static function getPropertiesAndValues()
    {
        $url = self::$BASE_URL.'/orders/get-properties-and-values';
        $resultJson = self::postGetRequest($url);
        $result = Json::decode($resultJson, false);
        return $result;
    }
    
    /**
     * @param string $cityName
     * @return array  
     */
    public static function getProducts($cityName)
    {
        $url = self::$BASE_URL.'/orders/get-products';
        $productsJson = self::postGetRequest($url, ['cityName' => $cityName]);
        $products = Json::decode($productsJson, false);
        return $products;
    }
    
    /**
     * @param string $name
     * @param string $cityName
     * @return array
     */
    public static function getProductsByName($name, $cityName)
    {
        $url = self::$BASE_URL.'/orders/get-products-by-name';
        $productsJson = self::postGetRequest($url, [
            'name' => $name, 
            'cityName' => $cityName
        ]);
        $products = Json::decode($productsJson, false);
        return $products;
    }
    
    /**
     * @param intger $categoryId
     * @return array
     */
    public static function getProductsByCategoryId($categoryId)
    {
        $url = self::$BASE_URL.'/orders/get-products-by-category-id';
        $productsJson = self::postGetRequest($url, ['categoryId' => $categoryId]);
        $products = Json::decode($productsJson, false);
        return $products;
    }
    
    /**
     * @param integer $productId
     * @return \stdClass
     */
    public static function getProductById($productId)
    {
        $url = self::$BASE_URL.'/orders/get-product-by-id';
        $productJson = self::postGetRequest($url, ['productId' => $productId]);
        $product = Json::decode($productJson, false);
        return $product;
    }
    
    /**
     * @param array $productIds Массив содержащий id продуктов.
     * @return array
     */
    public static function getProductsByIds($productIds)
    {
        $url = self::$BASE_URL.'/orders/get-products-by-ids';
        $productsIdsStr = implode(',', $productIds);
        $productsJson = self::postGetRequest($url, ['productIds' => $productsIdsStr]);
        $products = Json::decode($productsJson, false);
        return $products;
    }

    private static function postGetRequest($baseUrl, $params = [])
    {
        $ch = curl_init(); 
        if ($ch === false) {
            throw new \Exception(curl_error($ch));
        }
        
        $requestUrl = self::createUrl($baseUrl, $params); 

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch); 
        curl_close($ch);
        
        return $result;
    }
    
    private static function createUrl($baseUrl, $params = []) 
    {
        if (strpos($baseUrl, '?') === false) {
            $delimiter = '?';   
        } else {
            $delimiter = '&';
        }
        
        $result = $baseUrl;
        if (count($params) > 0) {
            foreach ($params as $paramName => $paramValue) {
                $result .= $delimiter.$paramName.'='.$paramValue;
                $delimiter = '&';
            }
        }
        
        return $result;
    }
    
}