<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\Vacancy;
use app\modules\personal\helpers\DateTimeHelper;

class VacancySearch extends Vacancy
{
    public $dateCreateFrom;
    public $dateCreateTo;
    public $datePublicFrom;
    public $datePublicTo;
      
    public function rules()
    {
        return [
            [
                [
                    'id', 
                    'post_id', 
                    'user_id', 
                    'depart_id', 
                    'state', 
                    'date_create', 
                    'date_public'
                    ], 
                    'integer'
            ],
            [['text'], 'safe'],
            $this->getDateIntervalsRules(),
        ];
    }

    public function search($params)
    {
        $query = Vacancy::find()
            ->joinWith(['settingsPost']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'depart_id' => $this->depart_id,
            'state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);
        $this->addDateIntervalsConditionsToQuery($query);
        
        return $dataProvider;
    }
    
    
    protected function getDateIntervalsRules() 
    {
        return  [
            [
                'dateCreateFrom', 
                'dateCreateTo', 
                'datePublicFrom', 
                'datePublicTo', 
            ], 
            'date', 
            'format' => 'php:d-m-Y',
        ];        
    }
    
    protected function addDateIntervalsConditionsToQuery($query)
    {
        $dateCreateFrom = $this->dateCreateFrom;
        $dateCreateTo = $this->dateCreateTo;
        $datePublicFrom = $this->datePublicFrom;
        $datePublicTo = $this->datePublicTo;
        
        $dateTimeFormat = 'd-m-Y H:i:s';
        if (!empty($dateCreateFrom)) {
            $dateCreateFrom = DateTimeHelper::getTimestampFromString($dateCreateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($dateCreateTo)) {
            $dateCreateTo = DateTimeHelper::getTimestampFromString($dateCreateTo.' 23:59:59', $dateTimeFormat);
        }
        if (!empty($datePublicFrom)) {
            $datePublicFrom = DateTimeHelper::getTimestampFromString($datePublicFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($datePublicTo)) {
            $datePublicTo = DateTimeHelper::getTimestampFromString($datePublicTo.' 23:59:59', $dateTimeFormat);
        }
        
        $query->andFilterWhere(['>=', 'date_create', $dateCreateFrom])
            ->andFilterWhere(['<=', 'date_create', $dateCreateTo])
            ->andFilterWhere(['>=', 'date_public', $datePublicFrom])
            ->andFilterWhere(['<=', 'date_public', $datePublicTo]);
        
        return $query;
    }
    
}
