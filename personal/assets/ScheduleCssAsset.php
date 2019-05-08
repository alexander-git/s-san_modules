<?php

namespace app\modules\personal\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ScheduleCssAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/personal/assets/scheduleCss';
    
    public $css = [
        'style.css',
    ];
    
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}