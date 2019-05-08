<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\helpers\DateTimeHelper;
use app\modules\cashdesks\models\PickercashTransact;


class PickercashTransactBaseSearch extends PickercashTransact
{
    public $dateCreateFrom;
    public $dateCreateTo;
    public $dateEndFrom;
    public $dateEndTo;
    
    public function __construct($config = array())
    {
        parent::__construct($config);
    }
 
        
    public function rules()
    {
        return [
            [
                [
                    'depart_id',
                    'type',
                    'state',
                    'picker_id',
                    'date_create', 
                    'date_end', 
                    'user_id', 
                ], 
                'integer'
            ],
            [['desc'], 'safe'],
            $this->getDateIntervalRules(),
        ];
    }
    
    public function search($params)
    {
        $query = PickercashTransact::find()
            ->joinWith(['banknotes']);

                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'type',
                    'state',
                    'date_create', 
                    'date_end',
                ],
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'depart_id' => $this->depart_id,
            'type' => $this->type,
            'state' => $this->state,
            'picker_id' => $this->picker_id,
            'user_id' => $this->user_id,
        ]);
        
        $query->andFilterWhere(['like', 'desc', $this->desc]);     
        $this->addDateIntervalsConditionsToQuery($query);        
        
        return $dataProvider;
    }
   
    protected function getDateIntervalRules()
    {
        return [
            [
                'dateCreateFrom', 
                'dateCreateTo', 
                'dateEndFrom', 
                'dateEndTo', 
            ], 
            'date', 
            'format' => 'php:d-m-Y'
        ];
    }
    
    protected function addDateIntervalsConditionsToQuery($query)
    {
        $dateCreateFrom = $this->dateCreateFrom;
        $dateCreateTo = $this->dateCreateTo;
        $dateEndFrom = $this->dateEndFrom;
        $dateEndTo = $this->dateEndTo;
        
        $dateTimeFormat = 'd-m-Y H:i:s';
        if (!empty($dateCreateFrom)) {
            $dateCreateFrom = DateTimeHelper::getTimestampFromString($dateCreateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($dateCreateTo)) {
            $dateCreateTo = DateTimeHelper::getTimestampFromString($dateCreateTo.' 23:59:59', $dateTimeFormat);
        }
        if (!empty($dateEndFrom)) {
            $dateEndFrom = DateTimeHelper::getTimestampFromString($dateEndFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($dateEndTo)) {
            $dateEndTo = DateTimeHelper::getTimestampFromString($dateEndTo.' 23:59:59', $dateTimeFormat);
        }
        
        $query->andFilterWhere(['>=', 'date_create', $dateCreateFrom])
            ->andFilterWhere(['<=', 'date_create', $dateCreateTo])
            ->andFilterWhere(['>=', 'date_end', $dateEndFrom])
            ->andFilterWhere(['<=', 'date_end', $dateEndTo]);
        
        return $query;
    }
    
}
