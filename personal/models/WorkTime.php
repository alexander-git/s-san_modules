<?php

namespace app\modules\personal\models;

use Yii;

use app\modules\personal\helpers\DateHelper;

/**
 * This is the model class for table "{{%personal_work_time}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property string $from
 * @property string $to
 */
class WorkTime extends \yii\db\ActiveRecord
{
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    const TIME_PATTERN = '/^[0-9]{2}:[0-9]{2}$/';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_work_time}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['user_id', 'integer'],
            
            ['date', 'required'],
            ['date', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['from', 'required'],
            ['from', 'match', 'pattern' => self::TIME_PATTERN],
            ['from', 'validateFrom'],
            
            ['to', 'required'],
            ['to', 'match', 'pattern' => self::TIME_PATTERN],
            ['to', 'validateTo'],
            ['to', 'validateFromToInterval'],
            ['to', 'validateIntersection'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'date' => 'Дата',
            'from' => 'От',
            'to' => 'До',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert) 
    {
        if (!empty($this->date)) { 
            $this->date = DateHelper::convertDateToDbFormat($this->date, self::DATE_FORMAT);
        }

        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->date)) {
            $this->date = DateHelper::convertDateFromDbFormat($this->date, self::DATE_FORMAT);
        }

        parent::afterFind();
    }
    
    
    public function validateFrom()
    {
        if ($this->hasErrors()) {  
            return;
        }
        
        $time = self::parseTimeString($this->from);
        if ($time->hours > 23) {
            $this->addError('from', 'Начальное время задано неверно.');
            return;
        }
        if ($time->minutes > 59) {
            $this->addError('from', 'Начальное время задано неверно.');
            return;
        }
    }
    
    public function validateTo()
    {
        if ($this->hasErrors()) {  
            return;
        } 
        
        $time = self::parseTimeString($this->to);
        if ($time->hours > 24) {
            $this->addError('to', 'Конечное время задано неверно.');
            return;
        }
        if ($time->minutes > 59) {
            $this->addError('to', 'Конечное время задано неверно.');
            return;
        }
        if ($time->hours === 24 && $time->minutes !== 0) {
            $this->addError('to', 'Конечное время задано неверно.'); 
            return;
        }
    }
    
    public function validateFromToInterval()
    {
        if ($this->hasErrors()) {  
            return;
        }
        
        $fromTime = self::parseTimeString($this->from);
        $toTime = self::parseTimeString($this->to);

        if ($fromTime->fullMinutes >= $toTime->fullMinutes) {
            $this->addError('to', 'Начальное время должно быть меньше конечного.');
        }  
    }
    
    public function validateIntersection()
    {
        if ($this->hasErrors()) {  
            return;
        }
        
        $errorMessage = 'Интервал не должнен пересекаться с уже существующими.';
 
        $dateDb = DateHelper::convertDateToDbFormat($this->date, self::DATE_FORMAT);
        $query = static::find()->where([
           'date' => $dateDb,
           'user_id' => $this->user_id,
        ]);

        if (!$this->isNewRecord) {
            $query->andWhere(['<>', 'id', $this->id]);
        }

        $workTimes = $query->all();

        $thisFromFullMinutes = self::parseTimeString($this->from)->fullMinutes;
        $thisToFullMinutes = self::parseTimeString($this->to)->fullMinutes;
        foreach ($workTimes as $workTime) {
            $fromFullMinutes = self::parseTimeString($workTime->from)->fullMinutes;
            $toFullMinutes = self::parseTimeString($workTime->to)->fullMinutes;

            if (
                $thisFromFullMinutes >= $fromFullMinutes && 
                $thisFromFullMinutes < $toFullMinutes
            ) {
                $this->addError('from', $errorMessage);
                return;
            }

            if (
                $thisToFullMinutes > $fromFullMinutes &&
                $thisToFullMinutes <= $toFullMinutes
            ) {
                $this->addError('to', $errorMessage);
                return;
            }
        }
    }
    
    public static function getWorkTimes($date, $userIds)
    {
        $searchDate = DateHelper::convertDateToDbFormat($date, self::DATE_FORMAT);
        
        return static::find()
            ->where(['date' => $searchDate])
            ->andWhere(['user_id' => $userIds])
            ->all();
        
        /*
        $result = [];
        foreach ($wokrTimes as $workTime)  {
            $item = new \stdClass();
            $item->id = $workTime->id;
            $item->user_id = $workTime->user_id;
            $item->date = $workTime->date;
            $item->from = $workTime->from;
            $item->to = $workTime->to;
            
            $result []= $item;
        }
        
        return $result; 
        */
    }
       
    private static function parseTimeString($timeStr) 
    {
        $hoursStr = substr($timeStr, 0, 2);
        $minutesStr = substr($timeStr, 3, 2);
        
        if ($hoursStr[0] === '0') {
            $hours = (int) $hoursStr[1];
        } else {
            $hours = (int) $hoursStr;
        }
            
        if ($minutesStr[0] === '0') {
            $minutes = (int) $minutesStr[1];
        } else {
            $minutes = (int) $minutesStr;
        }
        
        $result = new \stdClass();
        $result->hours = $hours;
        $result->minutes = $minutes;
        $result->fullMinutes = ($hours * 60) + $minutes; 
        
        return $result;
    }
    
}
