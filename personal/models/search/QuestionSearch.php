<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\personal\models\Question;
use app\modules\personal\models\PersonalApi;
use app\modules\personal\helpers\DateHelper;

class QuestionSearch extends Question
{
    public $dateFrom;
    public $dateTo;
    public $birthdayFrom;
    public $birthdayTo;

    const STATE_ALL_ACTIVE = 100;
    
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    
    public function rules()
    {
        return [
            [
                [
                    'post_id', 
                    'med_book', 
                    'children', 
                    'smoking', 
                    'about_us_id', 
                    'state'
                ], 
                'integer'
            ],
            [
                [
                    'name', 
                    'city', 
                    'address', 
                    'phone', 
                ], 
                'safe'
            ],
            $this->getBirthdayIntervalRules(),
        ];
    }

    public static function getStatesArray()
    {
        $baseStates = Question::getStatesArray();
        unset($baseStates[Question::STATE_CREATED]);
        unset($baseStates[Question::STATE_MAKE_MEDBOOK]);
        unset($baseStates[Question::STATE_ACCEPTED]);
                
        $newStates = [];
        $newStates[self::STATE_ALL_ACTIVE] = 'Все';
        
        return ArrayHelper::merge($newStates, $baseStates);
    }
    

    public function search($params)
    {
        $query = Question::find()
            ->from(['q' => Question::tableName()])
            ->joinWith(['settingsPost', 'aboutUsValue']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'q.id' => $this->id,
            'q.post_id' => $this->post_id,
            'q.med_book' => $this->med_book,
            'q.children' => $this->children,
            'q.smoking' => $this->smoking,
            'q.about_us_id' => $this->about_us_id,
        ]);

        $query->andFilterWhere(['like', 'q.name', $this->name])
            ->andFilterWhere(['like', 'q.city', $this->city])
            ->andFilterWhere(['like', 'q.phone', $this->phone]);
        
        $this->addStateConditionsToQuery($query);
        $this->addBirthdayIntervalsConditionsToQuery($query);

        return $dataProvider;
    }
     
    protected function getBirthdayIntervalRules() 
    {
        return  [
            [
                'birthdayFrom', 
                'birthdayTo', 
            ], 
            'date', 
            'format' => self::DATE_FORMAT_YII,
        ];        
    }
    
    protected function getDateIntervalsRules() 
    {
        return  [
            [
                'dateFrom', 
                'dateTo', 
            ], 
            'date', 
            'format' => self::DATE_FORMAT_YII,
        ];        
    }
    
    protected function addDateIntervalsConditionsToQuery($query)
    {
        $dateFrom = $this->dateFrom;
        $dateTo = $this->dateTo;
        
        if (!empty($dateFrom)) {
            $dateFrom = DateHelper::convertDateToDbFormat($dateFrom, self::DATE_FORMAT);
        }
        if (!empty($dateTo)) {
            $dateTo = DateHelper::convertDateToDbFormat($dateFrom, self::DATE_FORMAT);
        }
   
        $query->andFilterWhere(['>=', 'q.date', $dateFrom])
            ->andFilterWhere(['<=', 'q.date', $dateTo]);
        
        return $query;
    }
    
    protected function addBirthdayIntervalsConditionsToQuery($query)
    {
        $birthdayFrom = $this->birthdayFrom;
        $birthdayTo = $this->birthdayTo;
        
        if (!empty($birthdayFrom)) {
            $birthdayFrom = DateHelper::convertDateToDbFormat($birthdayFrom, self::DATE_FORMAT);
        }
        if (!empty($birthdayTo)) {
            $birthdayTo = DateHelper::convertDateToDbFormat($birthdayTo, self::DATE_FORMAT);
        }
   
        $query->andFilterWhere(['>=', 'q.birthday', $birthdayFrom])
            ->andFilterWhere(['<=', 'q.birthday', $birthdayTo]);
        
        return $query;
    }
    
    public function addStateConditionsToQuery($query)
    {
        if (empty($this->state)) {
            return;
        }
        
        $this->state = (int) $this->state;
        if ($this->state === self::STATE_ALL_ACTIVE) {
           $query->andFilterWhere(['<>', 'q.state', Question::STATE_REJECTED]);
        }
        if ($this->state === self::STATE_CALLBACK) {
            $time = PersonalApi::getCurrentTimestamp();
            $dateDbFormat = DateHelper::getDateDbFormatFromTimestamp($time);   
            $query->andFilterWhere(['q.state' => Question::STATE_CALLBACK]);
            $query->andFilterWhere(['q.date' => $dateDbFormat]);
        }
        if ($this->state === self::STATE_INTERVIEW) {
            $query->andFilterWhere(['q.state' => Question::STATE_INTERVIEW]);
        }
        if ($this->state === self::STATE_RESERVE) {
            $query->andFilterWhere(['q.state' => Question::STATE_RESERVE]);
        }
        if ($this->state === self::STATE_REJECTED) {
            $query->andFilterWhere(['q.state' => Question::STATE_REJECTED]);
        }
    }
    
}
