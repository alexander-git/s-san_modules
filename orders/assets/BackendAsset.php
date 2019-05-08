<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BackendAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/backend';
        
    public $css = [
        
    ];
    
    public $js = [
        'Backend.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}