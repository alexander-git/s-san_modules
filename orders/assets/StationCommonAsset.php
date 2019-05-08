<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class StationCommonAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/stationCommon';
    
     public $css = [
        'css/stationCommon.css',
    ];
    
    public $js = [
        'StationDataProcessorBase.js',    
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}