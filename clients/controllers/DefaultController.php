<?php

namespace app\modules\clients\controllers;

use yii\web\Controller;
use app\modules\clients\models\ClientsApi;

class DefaultController extends Controller
{
    protected function getApiToken()
    {
        return $this->module->apiToken;
    }
    
    protected function getHaveList()
    {
        return [
            0 => 'Нет',
            1 => 'Есть'
        ];
    }
    
    protected function getCitiesList()
    {
        return ClientsApi::getCitiesList();
    }
    
}
