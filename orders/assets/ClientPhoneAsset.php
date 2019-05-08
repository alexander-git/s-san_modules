<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ClientPhoneAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/clientPhone';
        
    public $css = [
        
    ];
    
    public $js = [
        'ClientPhoneScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}