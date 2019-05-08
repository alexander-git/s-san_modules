<?php

namespace app\modules\clients\assets;

use yii\web\AssetBundle;

class ActionColumnAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/clients/assets/actionColumn';
    
    public $css = [
        'main.css',
    ];
    
    public $js = [
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
}