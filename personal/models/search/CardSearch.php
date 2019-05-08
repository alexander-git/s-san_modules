<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\Card;
use app\modules\personal\helpers\DateHelper;

class CardSearch extends Card
{
    public $birthdayFrom;
    public $birthdayTo;
    public $dateEmploymentFrom;
    public $dateEmploymentTo;
    public $dateObtInputFrom;
    public $dateObtInputTo;
    public $dateObtFirstFrom;
    public $dateObtFirstTo;
    public $rateFrom;
    public $rateTo;
    
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id', 
                    'post_id', 
                    'depart_id', 
                    'med_book', 
                    'student', 
                    'docs_ok', 
                    'state'
                ], 
                'integer'
            ],
            [
                [
                    'name', 
                    'phone', 
                    'address', 
                ], 
                'safe'
            ],
            $this->getBirthdayIntervalRules(),
            $this->getDateIntervalsRules(),
            $this->getRateIntervalRules(),
        ];
    }

    public function search($params)
    {
        $query = Card::find()
            ->from(['c' => Card::tableName()])
            ->joinWith(['settingsPost']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'c.post_id' => $this->post_id,
            'c.depart_id' => $this->depart_id,
            'c.med_book' => $this->med_book,
            'c.student' => $this->student,
            'c.docs_ok' => $this->docs_ok,
            'c.state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'c.name', $this->name])
            ->andFilterWhere(['like', 'c.phone', $this->phone])
            ->andFilterWhere(['like', 'c.address', $this->address]);
        
        $this->addBirthdayIntervalsConditionsToQuery($query);
        $this->addDateIntervalsConditionsToQuery($query);
        $this->addRateIntervalCondtionsToQuery($query);

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
                'dateEmploymentFrom', 
                'dateEmploymentTo',
                'dateObtInputFrom',
                'dateObtInputTo',
                'dateObtFirstFrom',
                'dateObtFirstTo',
            ], 
            'date', 
            'format' => self::DATE_FORMAT_YII,
        ];        
    }
    
    protected function getRateIntervalRules()
    {
        return [
            [
                'rateFrom',
                'rateTo',
            ],
            'integer',
        ];
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
   
        $query->andFilterWhere(['>=', 'c.birthday', $birthdayFrom])
            ->andFilterWhere(['<=', 'c.birthday', $birthdayTo]);
        
        return $query;
    }
    
    protected function addDateIntervalsConditionsToQuery($query)
    {
        $dateEmpoymentFrom = $this->dateEmploymentFrom;
        $dateEmploymentTo = $this->dateEmploymentTo;
        $dateObtInputFrom = $this->dateObtInputFrom;
        $dateObtInoutTo = $this->dateObtInputTo;
        $dateObtFirstFrom = $this->dateObtFirstFrom;
        $dateObtFisrtTo = $this->dateObtFirstTo;
        
        if (!empty($dateEmpoymentFrom)) {
            $dateEmpoymentFrom = DateHelper::convertDateToDbFormat($dateEmpoymentFrom, self::DATE_FORMAT);
        }
        if (!empty($dateEmploymentTo)) {
            $dateEmploymentTo = DateHelper::convertDateToDbFormat($dateEmploymentTo, self::DATE_FORMAT);
        }
        
        if (!empty($dateObtInputFrom)) {
            $dateObtInputFrom = DateHelper::convertDateToDbFormat($dateObtInputFrom, self::DATE_FORMAT);
        }
        if (!empty($dateObtInoutTo)) {
            $dateObtInoutTo = DateHelper::convertDateToDbFormat($dateObtInoutTo, self::DATE_FORMAT);
        }
        if (!empty($dateObtFirstFrom)) {
            $dateObtFirstFrom = DateHelper::convertDateToDbFormat($dateObtFirstFrom, self::DATE_FORMAT);
        }
        if (!empty($dateObtFisrtTo)) {
            $dateObtFisrtTo = DateHelper::convertDateToDbFormat($dateObtFisrtTo, self::DATE_FORMAT);
        }
   
        $query->andFilterWhere(['>=', 'c.date_employment', $dateEmpoymentFrom])
            ->andFilterWhere(['<=', 'c.date_employment', $dateEmploymentTo])
            ->andFilterWhere(['>=', 'c.date_obt_input', $dateObtInputFrom])
            ->andFilterWhere(['<=', 'c.date_obt_input', $dateObtInoutTo])
            ->andFilterWhere(['>=', 'c.date_obt_first', $dateObtFirstFrom])
            ->andFilterWhere(['<=', 'c.date_obt_first', $dateObtFisrtTo]);
        
        return $query;
    }
    
    protected function addRateIntervalCondtionsToQuery($query)
    {
        $query->andFilterWhere(['>=', 'c.rate', $this->rateFrom])
            ->andFilterWhere(['<=', 'c.rate', $this->rateTo]);
        
        return $query;
    }
   
}
