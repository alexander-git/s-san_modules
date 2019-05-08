<?php

namespace app\modules\orders\models;

/**
 * This is the model class for table "{{%orders_options}}".
 *
 * @property string $id
 * @property string $name
 * 
 * @property OptionVal[] $optionVals
 */
class Option extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_options}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'required'],
            ['id', 'string', 'max' => 255],
            ['id', 'pattern', 'match' => '/^([a-z]_)+$/i'],
            ['id', 'unique'],
            
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
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
        $optionVals = $this->getOptionVals()->all();
        foreach($optionVals as $optionVal) {
            $optionVal->delete();
        }
        return parent::beforeDelete();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionVals()
    {
        return $this->hasMany(OptionVal::className(), ['option_id' => 'id']);
    }
    
}
