<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class KladrAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/kladr';
        
    public $css = [
        'jquery.kladr.min.css',
    ];
    
    public $js = [
        'jquery.kladr.min.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}