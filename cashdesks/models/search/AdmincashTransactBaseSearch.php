<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\helpers\DateTimeHelper;
use app\modules\cashdesks\models\AdmincashTransact;

class AdmincashTransactBaseSearch extends AdmincashTransact
{
    
    public $dateCreateFrom;
    public $dateCreateTo;
    public $dateEndFrom;
    public $dateEndTo;
    public $dateEditFrom;
    public $dateEditTo;
    
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
                    'administrator_id', 
                    'user_id',
                    'date_create', 
                    'date_end', 
                    'date_edit',  
                    'user_edit_id', 
                    'state'
                ], 
                'integer'
            ],           
            [['type_value', 'desc', ], 'safe'],
            $this->getDateIntervalsRules(),
        ];
    }
    
    public function search($params)
    {
        $query = AdmincashTransact::find()
            ->joinWith(['banknotes']);
                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'date_create', 
                    'date_end',
                    'date_edit',
                    'state',
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
            'state' => $this->state,
            'type' => $this->type,
            'administrator_id' => $this->administrator_id,
            'user_id' => $this->user_id,
            'user_edit_id' => $this->user_edit_id,
        ]);
     
        $query->andFilterWhere(['like', 'desc', $this->desc])      
            ->andFilterWhere(['like', 'type_value', $this->type_value]);
        
        $this->addDateIntervalsConditionsToQuery($query);
                
        return $dataProvider;
    }
    
    
    protected function getDateIntervalsRules() 
    {
        return 
        [
            [
                'dateCreateFrom', 
                'dateCreateTo', 
                'dateEndFrom', 
                'dateEndTo', 
                'dateEditFrom', 
                'dateEditTo'
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
        $dateEditFrom = $this->dateEditFrom;
        $dateEditTo = $this->dateEditFrom;
        
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
        if (!empty($dateEditFrom)) {
            $dateEditFrom = DateTimeHelper::getTimestampFromString($dateEditFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($dateEditTo)) {
            $dateEditTo = DateTimeHelper::getTimestampFromString($dateEditTo.' 23:59:59', $dateTimeFormat);
        }
        
        $query->andFilterWhere(['>=', 'date_create', $dateCreateFrom])
            ->andFilterWhere(['<=', 'date_create', $dateCreateTo])
            ->andFilterWhere(['>=', 'date_end', $dateEndFrom])
            ->andFilterWhere(['<=', 'date_end', $dateEndTo])
            ->andFilterWhere(['>=', 'date_edit', $dateEditFrom])
            ->andFilterWhere(['<=', 'date_edit', $dateEditTo]);
        
        return $query;
    }
   
}