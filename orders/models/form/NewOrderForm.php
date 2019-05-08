<?php

namespace app\modules\orders\models\form;

use yii\base\Model;

class NewOrderForm extends Model
{
    public $phone;
    
    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^8[0-9]{10}$/'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон'
        ]; 
    }
    
}