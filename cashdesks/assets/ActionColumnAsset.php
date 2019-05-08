<?php

namespace app\modules\cashdesks\assets;

use yii\web\AssetBundle;

class ActionColumnAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/cashdesks/assets/actionColumn';
    
    public $css = [
        'main.css',
    ];
    
    public $js = [
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
}