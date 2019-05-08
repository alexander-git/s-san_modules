<?php

namespace app\modules\orders\models\search;

use yii\data\ActiveDataProvider;
use app\modules\orders\models\Order;
use app\modules\orders\models\Stage;

class OrderReadySearch extends OrderSearch 
{  
    
    public function search($cityId, $params)
    {
        $query = Order::find()
            ->select([
                'id',
                'order_num',
                'stage_id',
                'start_date',
                'update_date',
                'end_date',
                'delivery_date',
                'delivery_time',
            ])
            ->where(['in', 'stage_id', self::getPossibleStageIds()])
            ->andWhere(['city_id' => $cityId]);
        
        $this->load($params);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'stage_id', 
                    'order_num',
                    'start_date', 
                    'update_date', 
                    'end_date',
                    'delivery_date' => [
                        'asc' => [
                            'delivery_date' => SORT_ASC, 
                            'delivery_time' => SORT_ASC
                        ],
                        'desc' => [
  
                            'delivery_date' => SORT_DESC, 
                            'delivery_time' => SORT_DESC
                        ],
                    ],
                    'delivery_time' => [
                        'asc' => [
                            'delivery_time' => SORT_ASC, 
                            'delivery_date' => SORT_ASC,
                        ],
                        'desc' => [ 
                            'delivery_time' => SORT_DESC, 
                            'delivery_date' => SORT_DESC
                        ],
                    ] 
                    
                ],
                'defaultOrder' => [
                    'delivery_date' => SORT_DESC, 
                ],
             ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }
 
        $query->andFilterWhere([
            'id' => $this->id,
            'stage_id' => $this->stage_id,
            'delivery_date' => $this->delivery_date,
            'delivery_time' => $this->delivery_time,
        ]);

        $query->andFilterWhere(['like', 'order_num', $this->order_num]);

        $this->addDateIntervalConditionsToQuery($query);
        $this->addTimeIntervalConditionsToQuery($query);
        
        return $dataProvider;
    }
           
    public static function getPossibleStageIds()
    {
        return [
            Stage::getDeliveringStageId(),
            Stage::getDeliveredStageId(),
        ];
    }
    
}
