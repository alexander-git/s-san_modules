<?php

namespace app\modules\orders\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ClientAddressAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/orders/assets/clientAddress';
        
    public $css = [
        
    ];
    
    public $js = [
        'ClientAddressController.js',
        'ClientAddressSelectors.js',
        'ClientAddressScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\orders\assets\KladrAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}