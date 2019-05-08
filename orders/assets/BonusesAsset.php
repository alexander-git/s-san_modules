<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BonusesAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/bonuses';
        
    public $css = [
        
    ];
    
    public $js = [
        'BonusesController.js',
        'BonusesSelectors.js',
        'BonusesBackend.js',
        'BonusesScript.js',
        
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\orders\assets\BackendAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}