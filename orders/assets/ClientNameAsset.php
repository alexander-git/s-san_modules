<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ClientNameAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/clientName';
        
    public $css = [
        
    ];
    
    public $js = [
        'ClientNameScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}