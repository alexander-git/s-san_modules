<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;

class ActionColumnAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/actionColumn';
    
    public $css = [
        'main.css',
    ];
    
    public $js = [
        
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
}