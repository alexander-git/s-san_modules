<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class StationPickAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/stationPick';
        
    public $css = [
        'css/stationPick.css',
    ];
    
    public $js = [
        'StationPickController.js',
        'StationPickSelectors.js',
        'StationPickBackend.js',
        'StationPickDataProcessor.js',
        'StationPickScript.js',
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