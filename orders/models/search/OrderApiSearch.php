<?php

namespace app\modules\orders\models\search;

use app\modules\orders\models\Order;

class OrderApiSearch extends Order
{
    public $count;
    public $startDateFrom;
    public $startDateTo;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'count',
                    'id', 
                    'stage_id', 
                    'city_id',
                    'startDateFrom', 
                    'startDateTo', 
                ], 
                'integer'
            ],
            [
                [
                    'order_num',  
                    'phone', 
                ], 
                'safe'
            ],
        ];
    }

    public function search($params)
    {
        $this->load($params);
                
        $query = Order::find()
            ->with(['orderItems', 'deliveryInfo', 'logRecords']);
        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'stage_id' => $this->stage_id,
            'order_num' => $this->order_num,
            'phone' => $this->phone,
        ]);

        $query->andFilterWhere(['>=', 'start_date', $this->startDateFrom])
            ->andFilterWhere(['>=', 'start_date', $this->startDateFrom]);
        
        $query->orderBy(['start_date' => SORT_ASC]);
        $query->limit($this->count);
        
        return $query->all();
    }
        
}
