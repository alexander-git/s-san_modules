<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class CartAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/cart';
        
    public $css = [
        
    ];
    
    public $js = [
        'CartController.js',
        'CartSelectors.js',
        'CartScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}