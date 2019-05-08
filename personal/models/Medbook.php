<?php

namespace app\modules\personal\models;

use Yii;

use app\modules\personal\helpers\DateHelper;

/**
 * This is the model class for table "{{%personal_medbook}}".
 *
 * @property integer $card_id
 * @property string $date_sanmin
 * @property string $date_sanmin_end
 * @property string $date_exam
 * @property string $date_exam_end
 *
 * @property PersonalCard $card
 */
class Medbook extends \yii\db\ActiveRecord
{
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_medbook}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['card_id', 'required'],
            ['card_id', 'integer'],
            [
                'card_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Card::className(), 
                'targetAttribute' => ['card_id' => 'id']
            ],
            ['card_id', 'unique'],
            
            ['date_sanmin', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['date_sanmin_end', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['date_exam', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['date_exam_end', 'date', 'format' => self::DATE_FORMAT_YII],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'card_id' => 'Карта',
            'date_sanmin' => 'Дата прохождения санитарного минимума',
            'date_sanmin_end' => 'Дата окончания санитарного минимума',
            'date_exam' => 'Дата прохождения медостомтра',
            'date_exam_end' => 'Дата окончания прохождения медосмотра',
        ];
    }
    
    public function beforeSave($insert) 
    {
        if (!empty($this->date_sanmin)) { 
            $this->date_sanmin = DateHelper::convertDateToDbFormat($this->date_sanmin, self::DATE_FORMAT);
        }
        if (!empty($this->date_sanmin_end)) { 
            $this->date_sanmin_end = DateHelper::convertDateToDbFormat($this->date_sanmin_end, self::DATE_FORMAT);
        }
        if (!empty($this->date_exam)) { 
            $this->date_exam = DateHelper::convertDateToDbFormat($this->date_exam, self::DATE_FORMAT);
        }
        if (!empty($this->date_exam_end)) { 
            $this->date_exam_end = DateHelper::convertDateToDbFormat($this->date_exam_end, self::DATE_FORMAT);
        }
        
        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->date_sanmin)) {
            $this->date_sanmin = DateHelper::convertDateFromDbFormat($this->date_sanmin, self::DATE_FORMAT);
        }
        if (!empty($this->date_sanmin_end)) {
            $this->date_sanmin_end = DateHelper::convertDateFromDbFormat($this->date_sanmin_end, self::DATE_FORMAT);
        }
        if (!empty($this->date_exam)) {
            $this->date_exam = DateHelper::convertDateFromDbFormat($this->date_exam, self::DATE_FORMAT);
        }
        if (!empty($this->date_exam_end)) {
            $this->date_exam_end = DateHelper::convertDateFromDbFormat($this->date_exam_end, self::DATE_FORMAT);
        }
        
        parent::afterFind();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'card_id']);
    }
    
}
