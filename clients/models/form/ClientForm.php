<?php

namespace app\modules\clients\models\form;

use yii\helpers\ArrayHelper;
use app\modules\clients\models\Client;

class ClientForm extends Client
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    public $newPassword;
    public $newPasswordRepeat;
     
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['newPassword', 'required', 'on' => self::SCENARIO_CREATE],
            ['newPassword', 'string', 'min' => 3, 'max' => 255],

            ['newPasswordRepeat', 'required', 'on' => self::SCENARIO_CREATE],
            ['newPasswordRepeat', 'string', 'min' => 3, 'max' => 255],            
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
        
        return ArrayHelper::merge(parent::rules(), $rules);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'newPassword' => 'Пароль',
            'newPasswordRepeat' => 'Повторите пароль', 
        ];
        
        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }
    
    public function scenarios() 
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_CREATE] = [
            'name',
            'fullname',
            'login',
            'email',
            'phone',
            'alterPhone',
            'birthday',
            'description',
            'note',
            'state',
            'newPassword',
            'newPasswordRepeat',
        ];
        
        
        $scenarios[self::SCENARIO_UPDATE] = $scenarios[self::SCENARIO_CREATE];
        
        return $scenarios;
    }
    
    public function beforeSave($insert) 
    {
        if (!empty($this->newPassword)) {
            $this->setPassword($this->newPassword);
        }
        
        return parent::beforeSave($insert);
    }
    
}