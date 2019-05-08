<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class MustacheAsset extends AssetBundle
{
    
    public $sourcePath = '@bower/mustache';
    
    public $css = [
        
    ];
    
    public $js = [
        'mustache.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',        
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}