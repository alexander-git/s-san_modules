<?php

namespace app\modules\orders;

class Module extends \yii\base\Module
{
    /**
     * Начальная часть атрибута src у тега img для товаров в меню и корзине.
     * @var string
     */
    public $productImageUrlPrefix = 'https://sandotplant.host/files/thumbnail/';
    
    /**
     * Конечная часть атрибута src у тега img для товаров в меню и корзине.
     * @var string
     */
    public $productImageUrlSuffix = '-180x180'; 
}
