<?php

namespace app\modules\picker\models;

use Yii;

/**
 * This is the model class for table "{{%picker_options}}".
 *
 * @property string $id
 * @property string $label
 *
 * @property PickerOptionsVal[] $pickerOptionsVals
 */
class Options extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picker_options}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'required'],
            ['id', 'unique'],
            ['id', 'string', 'max' => 255],
           
            ['label', 'required'],
            ['label', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Название',
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
        $optionsVals = $this->getOptionsVals()->all();
        foreach($optionsVals as $optionVal) {
            $optionVal->delete();
        }
        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionsVals()
    {
        return $this->hasMany(OptionsVal::className(), ['opt_id' => 'id']);
    }
        
}
