<?php

namespace app\modules\orders\models\form;

use yii\base\Model;
use yii\helpers\Json;

class OrderAddressForm extends Model
{
    public $street;
    public $home;
    public $appartment;
    public $floor;
    public $entrance;
    public $code;
    public $comment;
        
    public function rules()
    {
        return [
            ['street', 'required'],
            ['street', 'string', 'max' => 100],
            
            ['home', 'required'],
            ['home', 'string', 'max' => 100],
            
            ['appartment', 'required'],
            ['appartment', 'integer', 'min' => 1],
            
            ['floor', 'required'],
            ['floor', 'integer', 'min' => 1],
            
            ['entrance', 'string', 'max' => 100],
            
            ['code', 'string', 'max' => 100],
            
            ['comment', 'string', 'max' => 255],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'street' => 'Улица',
            'home' => 'Дом',
            'appartment' => 'Квартира',
            'floor' => 'Этаж',
            'entrance' => 'Подъезд',
            'code' => 'Код',
            'comment' => 'Примечание',
        ]; 
    }
    
    public function getAddressAsSting($cityName)
    {
        $firstPart = $cityName.', '.$this->street.', '.$this->home;
        if (!empty($this->appartment)) {
             $firstPart .= ' '.$this->appartment;
        }
        if (!empty($this->floor)) {
             $firstPart .= ' '.$this->floor;
        }
        
        $secondPart = '';
        if (!empty($this->entrance)) {
            $secondPart .= ' '.$this->entrance;
        }
        if (!empty($this->code)) {
            $secondPart .= ' '.$this->code;
        }
        if (!empty($this->comment)) {
            $secondPart .= ' '.$this->comment;
        }
        
        $result = $firstPart;
        if ($secondPart !== '') {
            $result .= '.'.$secondPart;
        }
        
        return $result;
    }
    
    public function getAddressJson($cityName)
    {
        $address = new \stdClass();
        $address->cityName = $cityName;
        $address->street = $this->street;
        $address->home = $this->home;
        $address->appartment = $this->appartment;
        $address->floor = $this->floor;
        $address->entrance = $this->entrance;
        $address->code = $this->code;
        $address->comment = $this->comment;
        return Json::encode($address);
    }
    
    public function fillOnJson($addressJson) 
    {
        $address = Json::decode($addressJson, false);
        $this->street = $address->street;
        $this->home = $address->home;
        $this->appartment = $address->appartment;
        $this->floor = $address->floor;
        $this->entrance = $address->entrance;
        $this->code = $address->code;
        $this->comment = $address->comment;        
    }
    
}