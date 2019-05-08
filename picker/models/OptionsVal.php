<?php

namespace app\modules\picker\models;

use Yii;

/**
 * This is the model class for table "{{%picker_options_val}}".
 *
 * @property string $opt_id
 * @property string $val
 * @property integer $depart_id
 *
 * @property PickerOptions $opt
 */
class OptionsVal extends \yii\db\ActiveRecord
{    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picker_options_val}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['opt_id', 'required'],
            ['opt_id', 'string', 'max' => 255],
            [
                'opt_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Options::className(),
                'targetAttribute' => ['opt_id' => 'id'], 
            ],
            
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            
            ['val', 'required'],
            ['val', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'opt_id' => 'Опция',
            'val' => 'Значение',
            'depart_id' => 'Департамент',
            'optionLabel' => 'Опция',
        ];
    }

    public function getOptionLabel()
    {
        return $this->option->label;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Options::className(), ['id' => 'opt_id']);
    }
    
    public static function deleteByDepartmentId($departmentId)
    {
        static::deleteAll(['depart_id' => $departmentId]);
    }
    
    /**
     * @return array ассоциативный массив OptionsVal с ключами равными opt_id. 
     */
    
    public static function getPaymentOptionsValsByDepartmentId($departmentId)
    {
        // Опции которые нам нужны.
        $paymentOptionsIds = self::getPaymentOptionsIds();
      
        // Сначала выберем опции по умолчанию.
        $result = OptionsVal::find()
            ->where(['depart_id' => PickerApi::getDefaultDepartmentId()])
            ->andWhere(['in', 'opt_id', $paymentOptionsIds])
            ->indexBy('opt_id')
            ->all();

        // Выберем опции по для нужного департамента.
        $departmentOptionsVals = OptionsVal::find()
            ->where(['depart_id' => $departmentId])
            ->andWhere(['in', 'opt_id', $paymentOptionsIds])
            ->indexBy('opt_id')
            ->all();
        
        // Если есть заменим настройки по умолчанию
        // настройками для конкретного депратамента.
        foreach ($departmentOptionsVals as $key => $value) {
            $result[$key] = $value;
        }
        
        return $result;
    }
    
    private static function getPaymentOptionsIds()
    {
        return [
            'pay_day_courier', 
            'pay_even_courier', 
            'pay_dop_courier', 
            'pay_trip'
        ];
    }
    
}
