<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;

class OrderCssAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/orderCss';
    
    public $css = [
        'main.css',
    ];
    
    public $js = [
        
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
}