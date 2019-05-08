<?php

namespace app\modules\personal\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ScheduleAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/personal/assets/schedule';
    
    public $css = [
        
    ];
    
    public $js = [
        'ScheduleSelectors.js',
        'ScheduleItems.js',
        'ScheduleController.js',
        'ScheduleBackend.js',
        'ScheduleScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}