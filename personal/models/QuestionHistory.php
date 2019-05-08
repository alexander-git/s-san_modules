<?php

namespace app\modules\personal\models;

use Yii;

/**
 * This is the model class for table "{{%personal_question_history}}".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $state
 * @property string $text
 *
 * @property Question $question
 */
class QuestionHistory extends \yii\db\ActiveRecord
{
    const SCENARIO_TEXT_REQUIRED = 'textRequired';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_question_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['question_id', 'required'],
            ['question_id', 'integer'],
            [
                'question_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Question::className(), 
                'targetAttribute' => ['question_id' => 'id']
            ],
            
            ['date_change', 'required'],
            ['date_change', 'integer'],
            
            
            ['text', 'required', 'on' => self::SCENARIO_TEXT_REQUIRED],
            ['text', 'string', 'max' => 255],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(Question::getStatesArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Анкета',
            'date_change' => 'Дата изменения',
            'state' => 'Состояние',
            'text' => 'Комментарий',
        ];
    }
    
    public function getStateName()
    {
        return Question::getStatesArray()[$this->state];
    }
         
    public function getIsCreated()
    {
        return $this->state === self::STATE_CREATED;
    }
    
    public function getIsCallback()
    {
        return $this->state === self::STATE_CALLBACK;
    }
    
    public function getIsRejected()
    {
        return $this->state === self::STATE_REJECTED;
    }
    
    public function getIsReserve()
    {
        return $this->state === self::STATE_RESERVE;
    }
    
    public function getIsInterview()
    {
        return $this->state === self::STATE_INTERVIEW;
    }
    
    public function getIsMakeMedbook()
    {
        return $this->state === self::STATE_MAKE_MEDBOOK;
    }
    
    public function getIsAccepted()
    {
        return $this->state === self::STATE_ACCEPTED;
    }
    
    public function getDepartmentName()
    {
        return PersonalApi::getDepartmentName($this->depart_id);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }
    
}
