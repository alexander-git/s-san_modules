<?php

namespace app\modules\personal\models;

use Yii;

/**
 * This is the model class for table "{{%personal_vacancy}}".
 *
 * @property integer $id
 * @property string $text
 * @property integer $post_id
 * @property integer $user_id
 * @property integer $depart_id
 * @property integer $state
 * @property integer $date_create
 * @property integer $date_public
 *
 * @property SettingsPost $settingsPost
 */
class Vacancy extends \yii\db\ActiveRecord
{
    const STATE_CREATED = 0;
    const STATE_PUBLISHED = 1;
    const STATE_UNPUBLISHED = 2;
    
    const SCENARIO_UPDATE = 'update';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_vacancy}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            
            ['post_id', 'required'],
            ['post_id', 'integer'],
            [
                'post_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => 
                SettingsPost::className(), 
                'targetAttribute' => ['post_id' => 'id']
            ],
                        
            ['user_id', 'required'],
            ['user_id', 'integer'],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
            
            ['text', 'required'],
            ['text', 'string'],
            
            ['date_create', 'required'],
            ['date_create', 'integer'],
            
            ['date_public', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Описание',
            'post_id' => 'Должность',
            'user_id' => 'Пользователь',
            'depart_id' => 'Департамент',
            'state' => 'Состояние',
            'date_create' => 'Дата создания',
            'date_public' => 'Дата публикации/снятия',
        ];
    }
    
    public function scenarios() 
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_UPDATE] = [            
            'post_id',
            'depart_id',
            'text',
            'state',
            '!user_id',
            '!date_update',
        ];
        
        return $scenarios;
    }
        
    public static function getStatesArray()
    {
        return [
            self::STATE_CREATED => 'Создана',
            self::STATE_PUBLISHED => 'Опубликована',
            self::STATE_UNPUBLISHED => 'Не опубликована',
        ];
    }
        
    public function getStateName()
    {
        return self::getStatesArray()[$this->state];
    }
              
    public function getIsCreated()
    {
        return $this->state === self::STATE_CREATED;
    }
    
    public function getIsPublished()
    {
        return $this->state === self::STATE_PUBLISHED;
    }
    
    public function getIsUnpublished()
    {
        return $this->state === self::STATE_UNPUBLISHED;
    }
    
    public function getDepartmentName()
    {
        return PersonalApi::getDepartmentName($this->depart_id);
    }
    
    public function getUserName()
    {
        return PersonalApi::getUserName($this->user_id);
    }
    
    public function getPostName()
    {
        return $this->settingsPost->name;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettingsPost()
    {
        return $this->hasOne(SettingsPost::className(), ['id' => 'post_id']);
    }
    
    public static function createVacancy($vacancyModel, $userId)
    {
        $time = PersonalApi::getCurrentTimestamp();
        
        $vacancyModel->user_id = $userId;
        $vacancyModel->date_create =  $time;
        $vacancyModel->date_public = null;
        $vacancyModel->state = self::STATE_CREATED;
        
        return $vacancyModel->save();
    }
    
    public static function updateVacancy($vacancyModel, $userId)
    {
        $time = PersonalApi::getCurrentTimestamp();
        
        $vacancyModel->user_id = $userId;
        $vacancyModel->date_public = $time;
        
        return $vacancyModel->save();
    }
    
}
