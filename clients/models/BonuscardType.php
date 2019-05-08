<?php

namespace app\modules\clients\models;

use Yii;

/**
 * This is the model class for table "{{%clients_bonuscard_type}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $discount
 * @property integer $bonusquan
 * @property integer $minmoney
 *
 * @property ClientsBonuscard[] $clientsBonuscards
 */
class BonuscardType extends \yii\db\ActiveRecord
{
    
    private $_bonuscardsCount;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%clients_bonuscard_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            
            ['discount', 'required'],
            ['discount', 'integer'],
            
            ['bonusquan', 'required'],
            ['bonusquan', 'integer'],
            
            ['minmoney', 'required'],
            ['minmoney', 'integer'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'discount' => 'Скидка',
            'bonusquan' => 'Величина бонусов',
            'minmoney' => 'Минимальная сумма',
            'bonuscardsCount' => 'Число карт',
        ];
    }

    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
        ];
    }
    
    public function beforeDelete()
    {
        $bonuscards = $this->getBonuscards()->with(['client'])->all();
        foreach ($bonuscards as $bonuscard) {
            $client = $bonuscard->client;
            $bonuscard->delete();
            $client->cardnum = null;
            if (!$client->save()) {
                throw new \Exception();
            }
        }
        
        return parent::beforeDelete();
    }
    
    public function setBonuscardsCount($value)
    {
        return $this->_bonuscardsCount = $value;
    }
    
    public function getBonuscardsCount()
    {
        if ($this->_bonuscardsCount === null) {
            $this->_bonuscardsCount = $this->getBonuscards()->count();
        }
        
        return $this->_bonuscardsCount;
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonuscards()
    {
        return $this->hasMany(Bonuscard::className(), ['type' => 'id']);
    }
    
}
