<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\helpers\DateTimeHelper;
use app\modules\cashdesks\models\AccountableTransact;


class AccountableTransactBaseSearch extends AccountableTransact
{
    public $dateCreateFrom;
    public $dateCreateTo;
    public $sumFrom;
    public $sumTo;
    
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
                    'date_create', 
                    'picker_id',
                    'user_id', 
                    'sum',
                ], 
                'integer'
            ],
            [['desc'], 'safe'],
            $this->getDateIntervalRules(),
            $this->getSumIntervalRules(),
        ];
    }
    
    public function search($params)
    {
        $query = AccountableTransact::find();
                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'type',
                    'date_create', 
                    'sum',
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
            'picker_id' => $this->picker_id,
            'user_id' => $this->user_id,
        ]);
        
        $query->andFilterWhere(['like', 'desc', $this->desc]);

        $this->addDateIntervalConditionsToQuery($query);
        $this->addSumConditionsToQuery($query);
                
        return $dataProvider;
    }

    protected function getDateIntervalRules()
    {
        return [
            [
                'dateCreateFrom', 
                'dateCreateTo', 
            ], 
            'date', 
            'format' => 'php:d-m-Y',
        ];
    }
    
    protected function getSumIntervalRules()
    {
        return [['sumFrom', 'sumTo'], 'integer'];
    }
    
    protected function addDateIntervalConditionsToQuery($query) 
    {
        $dateCreateFrom = $this->dateCreateFrom;
        $dateCreateTo = $this->dateCreateTo;
     
        $dateTimeFormat = 'd-m-Y H:i:s';
        if (!empty($dateCreateFrom)) {
            $dateCreateFrom = DateTimeHelper::getTimestampFromString($dateCreateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($dateCreateTo)) {
            $dateCreateTo = DateTimeHelper::getTimestampFromString($dateCreateTo.' 23:59:59', $dateTimeFormat);
        }
        
        $query->andFilterWhere(['>=', 'date_create', $dateCreateFrom])
            ->andFilterWhere(['<=', 'date_create', $dateCreateTo]);
        
        return $query;
    }
    
    protected function addSumConditionsToQuery($query) 
    {   
        $query->andFilterWhere(['>=', 'sum', $this->sumFrom])
            ->andFilterWhere(['<=', 'sum', $this->sumTo]);
        
        return $query;
    }
            
}
