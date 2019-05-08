<?php

namespace app\modules\personal\models;

use Yii;

use yii\helpers\Json;
use app\modules\personal\helpers\DateHelper;

/**
 * This is the model class for table "{{%personal_question}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $post_id
 * @property string $birthday
 * @property string $city
 * @property string $address
 * @property string $phone
 * @property string $work_time
 * @property integer $med_book
 * @property integer $children
 * @property integer $smoking
 * @property integer $about_us_id
 * @property string $experience
 * @property string $hobby
 * @property string $date
 * @property integer $state
 *
 * @property AboutUsValue $aboutUsValue
 * @property SettingsPost $settingsPost
 * @property QuestionHistory[] $questionHistories
 */
class Question extends \yii\db\ActiveRecord
{
    const STATE_CREATED = 0;
    const STATE_CALLBACK = 1;
    const STATE_REJECTED = 2;
    const STATE_RESERVE = 3;
    const STATE_INTERVIEW = 4;
    const STATE_MAKE_MEDBOOK = 5;
    const STATE_ACCEPTED = 6;
    
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CALLBACK = 'callback';
    const SCENARIO_REJECT = 'reject';
    const SCENARIO_RESERVE = 'reserve';
    const SCENARIO_INTERVIEW = 'interview';
    const SCENARIO_ACCEPT = 'accept';
    
    
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_question}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            
            ['post_id', 'required', 'on' => [
                self::SCENARIO_RESERVE,
                self::SCENARIO_INTERVIEW,
                self::SCENARIO_ACCEPT,
            ]],
            ['post_id', 'integer'],
            [
                'post_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => 
                SettingsPost::className(), 
                'targetAttribute' => ['post_id' => 'id']
            ],
            
            ['birthday' , 'required', 'on' => [self::SCENARIO_ACCEPT]],
            ['birthday', 'date', 'format' => 'php:d-m-Y'],
            
            ['city', 'required', 'on' => [self::SCENARIO_ACCEPT]],
            ['city', 'string', 'max' => 255],
            
            ['address', 'required', 'on' => [self::SCENARIO_ACCEPT]],
            ['address', 'string', 'max' => 255],
            
            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
            
            ['work_time', 'required', 'on' => [self::SCENARIO_ACCEPT]],
            ['work_time', 'safe'],
            
            ['med_book', 'boolean'],
            ['med_book', 'default', 'value' => 0],
            
            ['children', 'boolean'],
            ['children', 'default', 'value' => 0],
            
            ['smoking', 'boolean'],
            ['smoking', 'default', 'value' => 0],
            
            ['about_us_id', 'required', 'on' => [self::SCENARIO_ACCEPT]],
            ['about_us_id', 'integer'],
            [
                'about_us_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => 
                AboutUsValue::className(), 
                'targetAttribute' => ['about_us_id' => 'id']
            ],
            
            ['experience', 'string'],
            
            ['hobby', 'string'],
            
            ['date', 'required', 'on' => [
                self::SCENARIO_CALLBACK,
                self::SCENARIO_INTERVIEW,
            ]],
            ['date', 'date', 'format' => 'php:d-m-Y'],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ФИО',
            'post_id' => 'Должность',
            'birthday' => 'Дата рождения',
            'city' => 'Город',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'work_time' => 'Желаемое время',
            'med_book' => 'Мед книжка',
            'children' => 'Дети',
            'smoking' => 'Курение',
            'about_us_id' => 'Как узнали о нас',
            'experience' => 'Опыт',
            'hobby' => 'Хобби',
            'date' => 'Дата',
            'state' => 'Состояние',
        ];
    }

    public static function getStatesArray()
    {
        return [
            self::STATE_CREATED => 'Создана',
            self::STATE_CALLBACK => 'Перезвонить',
            self::STATE_REJECTED => 'Отказать',
            self::STATE_RESERVE => 'Резерв',
            self::STATE_INTERVIEW => 'Собеседование',
            self::STATE_MAKE_MEDBOOK => 'Сделать мед книжку',
            self::STATE_ACCEPTED => 'Принять',
        ];
    }
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $withDate = [
            'name',
            'post_id',
            'birthday',
            'city',
            'address',
            'phone',
            'work_time',
            'med_book',
            'children',
            'smoking',
            'about_us_id',
            'experience',
            'hobby',
            'date',
            '!state'
        ];
        
        $withoutDate = [
            'name',
            'post_id',
            'birthday',
            'city',
            'address',
            'phone',
            'work_time',
            'med_book',
            'children',
            'smoking',
            'about_us_id',
            'experience',
            'hobby',
            '!date',
            '!state'  
        ];
        
        $scenarios[self::SCENARIO_CREATE] = $withoutDate;
        $scenarios[self::SCENARIO_CALLBACK] = $withDate;
        $scenarios[self::SCENARIO_REJECT] = $withoutDate;
        $scenarios[self::SCENARIO_RESERVE] = $withoutDate;
        $scenarios[self::SCENARIO_INTERVIEW] = $withDate;
        $scenarios[self::SCENARIO_ACCEPT] = $withoutDate;
        $scenarios[self::SCENARIO_UPDATE] = $withoutDate;
                
        return $scenarios;
    }
    
    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
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
    
    public function getPostName()
    {
        if ($this->settingsPost === null) {
            return null;
        }
        
        return $this->settingsPost->name;
    }
    
    public function getAboutUsValueName()
    {
        if ($this->aboutUsValue === null) {
            return null;
        }
        
        return $this->aboutUsValue->name;
    }
    
    public static function getWorkTimeArray()
    {
        return [
            'morning' => 'Утро',
            'day' => 'День',
            'evening' => 'Вечер',
        ];
    }
    
    public function getWorkTimeName()
    {
        $workTimeArray = self::getWorkTimeArray();
 
        $result = [];
        if ($this->work_time !== null && $this->work_time !== '') {
            foreach ($this->work_time as $value) {
                $result []= $workTimeArray[$value];
            }
        }
        
        if (count($result) === 0) {
            return null;    
        } else {
            return implode(', ', $result);
        }
    }
    
    public function beforeDelete() 
    {
        $questionHistories = $this->questionHistories;
        foreach ($questionHistories as $questionHistory) {
            $questionHistory->delete();
        }
        
        return parent::beforeDelete();
    }
    
    public function beforeSave($insert) 
    {
        if (!empty($this->birthday)) { 
            $this->birthday = DateHelper::convertDateToDbFormat($this->birthday, self::DATE_FORMAT);
        }
        if (!empty($this->date)) { 
            $this->date = DateHelper::convertDateToDbFormat($this->date, self::DATE_FORMAT);
        }
        
        // Закодируем в json.
        $workTime = [
            'morning' => false,
            'day' => false,
            'evening' => false,
        ];
        
        if (!empty($this->work_time)) {
            foreach ($this->work_time as $value) {
               $workTime[$value] = true;
            }
        }
        
        $this->work_time = Json::encode($workTime);
        
        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->birthday)) {
            $this->birthday = DateHelper::convertDateFromDbFormat($this->birthday, self::DATE_FORMAT);
        }
        if (!empty($this->date)) {
            $this->date = DateHelper::convertDateFromDbFormat($this->date, self::DATE_FORMAT);
        }
           
        if ($this->work_time !== null && $this->work_time !== '') {
            $workTime = Json::decode($this->work_time);
            $workTimeValue = [];
            foreach ($workTime as $key => $value) {
                if ($value) {
                    $workTimeValue []= $key;
                }
            }
            $this->work_time = $workTimeValue;
        }
                
        parent::afterFind();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAboutUsValue()
    {
        return $this->hasOne(AboutUsValue::className(), ['id' => 'about_us_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettingsPost()
    {
        return $this->hasOne(SettingsPost::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionHistories()
    {
        return $this->hasMany(QuestionHistory::className(), ['question_id' => 'id']);
    }
    
    public static function createQuestion($question)
    {
        $questionHistory = new QuestionHistory();
        return self::performStateOperation($question, $questionHistory, self::STATE_CREATED);
    }
    
    public static function callbackQuestion($question, $questionHistory)
    {
        return self::performStateOperation($question, $questionHistory, self::STATE_CALLBACK);
    }
    
    
    public static function reserveQuestion($question, $questionHistory)
    {
        return self::performStateOperation($question, $questionHistory, self::STATE_RESERVE);
    }
    
    public static function interviewQuestion($question, $questionHistory)
    {
        return self::performStateOperation($question, $questionHistory, self::STATE_INTERVIEW);
    }
    
    public static function rejectQuestion($question, $questionHistory)
    {
        return self::performStateOperation($question, $questionHistory, self::STATE_REJECTED);
    }
    
    public static function acceptQuestion($question, $questionHistory)
    {
        return self::performStateOperation($question, $questionHistory, self::STATE_ACCEPTED);
    }
    
    public static function makeMedbookQuestion($question) 
    {
        $question->med_book = false;
        $questionHistory = new QuestionHistory();
        return self::performStateOperation($question, $questionHistory, self::STATE_MAKE_MEDBOOK);  
    }
    
    
    public static function makeMedbookCompleteQuestion($question) 
    {
        $question->med_book = true;
        return $question->save();
    }
    
    public static function returnToCreateStateQuestion($question, $questionHistory)
    {
        $transaction = Question::getDb()->beginTransaction();
        try {
            $time = PersonalApi::getCurrentTimestamp();
                
            //$question->date = Yii::$app->formatter->format($time, ['date', self::DATE_FORMAT_YII]);
            $question->state = self::STATE_CREATED;
            
            if (!$question->save()) {
                $transaction->rollBack();
                return false;
            }
            $questionHistory->question_id = $question->id;
            $questionHistory->state = self::STATE_CREATED;
            $questionHistory->date_change = $time;
            
            if (!$questionHistory->save()) {
                $transaction->rollBack();
                return false;
            }
    
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;            
        }
    }
    
    private static function performStateOperation($question, $questionHistory, $state)
    {
        $transaction = Question::getDb()->beginTransaction();
        try {
            $time = PersonalApi::getCurrentTimestamp();
                    
            if ($state === self::STATE_CREATED) { 
                // В других случаях дата приходит из формы.
                $question->date = Yii::$app->formatter->format($time, ['date', self::DATE_FORMAT_YII]);
            }
            
            $question->state = $state;
            
            if (!$question->save()) {
                $transaction->rollBack();
                return false;
            }
            $questionHistory->question_id = $question->id;
            $questionHistory->state = $state;
            $questionHistory->date_change = $time;
            
            if (!$questionHistory->save()) {
                $transaction->rollBack();
                return false;
            }
    
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;            
        }
    }
    
}
