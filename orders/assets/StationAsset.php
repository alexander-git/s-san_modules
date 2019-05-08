<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class StationAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/station';
        
    public $css = [
        'css/station.css',
    ];
    
    public $js = [
        'StationController.js',
        'StationSelectors.js',
        'StationBackend.js',
        'StationDataProcessor.js',
        'StationScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\orders\assets\BackendAsset',
        'app\modules\orders\assets\StationCommonAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}