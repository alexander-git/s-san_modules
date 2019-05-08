<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class InfoAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/info';
        
    public $css = [
        
    ];
    
    public $js = [
        'InfoController.js',
        'InfoSelectors.js',
        'InfoScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}