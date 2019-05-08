<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class MenuAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/menu';
        
    public $css = [
        
    ];
    
    public $js = [
        'MenuController.js',
        'MenuSelectors.js',
        'MenuBackend.js',
        'MenuScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\orders\assets\BackendAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}