<?php

namespace app\modules\clients;

class Module extends \yii\base\Module
{
    /**
     * @var string Используется в ClientsApiController для вычисления хэша 
     *      который, должен быть передан при вызове его методов. 
     *  
     */
    public $apiToken = 'password';
    
}
