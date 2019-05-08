<?php

namespace app\modules\picker\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BanknotesFormAsset extends AssetBundle
{
    
    public $sourcePath = '@app/modules/picker/assets/banknotesForm';
    
    public $css = [
        
    ];
    
    public $js = [
        'BanknotesForm.js',
        'BanknotesFormScript.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}