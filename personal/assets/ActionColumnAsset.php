<?php

namespace app\modules\personal\assets;

use yii\web\AssetBundle;

class ActionColumnAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/personal/assets/actionColumn';
    
    public $css = [
        'main.css',
    ];
    
    public $js = [
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
}