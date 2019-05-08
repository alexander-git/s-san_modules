<?php

namespace app\modules\personal\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ModalAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/personal/assets/modal';
    
    public $css = [
        
    ];
    
    public $js = [
        'modal.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',        
        'yii\bootstrap\BootstrapAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}