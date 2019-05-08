<?php

namespace app\modules\orders\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\modules\orders\helpers\DateTimeHelper;
use app\modules\orders\models\LogRecord;



class LogRecordSearch extends LogRecord
{
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    public $dateFrom;
    public $dateTo;
    
    public function rules()
    {
        return [
            [['id', 'stage_id', 'date'], 'integer'],
            ['comment', 'safe'],
            $this->getDateIntervalsRules(),
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($orderId, $params)
    {
        $query = LogRecord::find()
            ->where(['order_id' => $orderId]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'stage_id' => $this->stage_id,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);
        $this->addDateIntervalConditionsToQuery($query);
        
        return $dataProvider;
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
    
    protected function addDateIntervalConditionsToQuery($query)
    {
        $dateFrom = $this->dateFrom ;
        $dateTo = $this->dateTo;

        $dateTimeFormat = self::DATE_FORMAT.' H:i:s';
        if (!empty($dateFrom)) {
            $dateFrom = DateTimeHelper::getTimestampFromString($dateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($dateTo)) {
            $dateTo = DateTimeHelper::getTimestampFromString($dateTo.' 23:59:59', $dateTimeFormat);
        }
   
        $query->andFilterWhere(['>=', 'date', $dateFrom])
            ->andFilterWhere(['<=', 'date', $dateTo]);

        return $query;
    }
    
}
