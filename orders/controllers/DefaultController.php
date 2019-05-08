<?php

namespace app\modules\orders\controllers;

use yii\web\Controller;
use app\modules\orders\models\OrdersApi;

class DefaultController extends Controller
{   
    protected function getCitiesList()
    {
        return OrdersApi::getCitiesList();
    }
    
    protected function getCityNameById($cityId)
    {
        return OrdersApi::getCityNameById($cityId);
    }
    
}
