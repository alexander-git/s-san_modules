<?php

namespace app\modules\orders\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\modules\orders\models\DateTimeConstsInterface;
use app\modules\orders\helpers\DateHelper;
use app\modules\orders\helpers\TimeHelper;
use app\modules\orders\helpers\DateTimeHelper;
use app\modules\orders\models\Order;
use app\modules\orders\models\Stage;
//use app\modules\orders\models\OrdersApi;


class OrderSearch extends Order implements DateTimeConstsInterface
{
    public $startDateFrom;
    public $startDateTo;
    public $updateDateFrom;
    public $updateDateTo;
    public $endDateFrom;
    public $endDateTo;
    public $deliveryDateFrom;
    public $deliveryDateTo;
    public $deliveryTimeFrom;
    public $deliveryTimeTo;
    
    public $itemsCountFrom;
    public $itemsCountTo;
    public $totalPriceFrom;
    public $totalPriceTo;
    public $taxFrom;
    public $taxTo;
    public $totalPayFrom;
    public $totalPayTo;
    public $personNumFrom;
    public $personNumTo;
    public $returnSumFrom;
    public $returnSumTo;
    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id', 
                    'client_id', 
                    'user_id', 
                    'city_id',
                    'stage_id', 
                    'payment_type', 
                    'is_postponed', 
                    'is_paid', 
                    'is_deleted', 
                    //'start_date', 
                    //'update_date', 
                    //'end_date', 
                    //'person_num',
                    //'tax', 
                    //'items_count', 
                    //'total_price', 
                    //'total_pay', 
                    //'return_sum'
                ], 
                'integer'
            ],
            [
                [
                    'order_num', 
                    'recipient', 
                    'phone', 
                    'alter_phone', 
                    'address', 
                    'comment', 
                ], 
                'safe'
            ],
            //['delivery_date', 'date', 'format' => self::DATE_FORMAT_YII],
            //['delivery_time', 'date', 'format' => self::TIME_FORMAT_YII],
            $this->getDateIntervalsRules(),
            $this->getTimeIntervalRules(),
            $this->getIntegerValuesIntervalRules(),
        ];
    }
    
    public function attributes() 
    {
        $thisAttributes = [
            'startDateFrom',
            'startDateTo',
            'updateDateFrom',
            'updateDateTo',
            'endDateFrom',
            'endDateTo',
            'deliveryDateFrom',
            'deliveryDateTo',
            'deliveryTimeFrom',
            'deliveryTimeTo',

            'itemsCountFrom',
            'itemsCountTo',
            'totalPriceFrom',
            'totalPriceTo',
            'taxFrom',
            'taxTo',
            'totalPayFrom',
            'totalPayTo',
            'personNumFrom',
            'personNumTo',
            'returnSumFrom',
            'returnSumTo',
            
        ];
        
        return ArrayHelper::merge($thisAttributes, parent::attributes());
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        //$currentTime = OrdersApi::getCurrentTimestamp();
        //$fromDeliveryDate = DateHelper::getDateDbFormatFromTimestamp($currentTime);
        //$fromDeliveryTime = TimeHelper::getTimeDbFormatFromTimestamp($currentTime);
        
        // isNewStage используется для упорядочивания при котором сначала
        // идут новые заказы, а потом уже все остальные соглансно дате доставки.
        $newStageId = Stage::getNewStageId();
        $deliveredStageId = Stage::getDeliveredStageId();
        $canceledStageId = Stage::getCanceledStageId();
        $query = Order::find()
        ->select([
            '*', 
            (new Expression('(stage_id = '.$newStageId.') AS isNewStage')),
        ]);
        
        /*
        $query->where([
               'or', 
               ['=', 'stage_id', $newStageId], 
               [
                   'and', 
                   ['=', 'delivery_date', $fromDeliveryDate], 
                   ['>=', 'delivery_time', $fromDeliveryTime],
               ],
               ['>', 'delivery_date', $fromDeliveryDate], 
           ]); 
           */
        
        $this->load($params);
        
        // Поиск нужен по абсолютно всем заказам, при этом если в поиск ничего 
        // не введено отображать заказы которые не доставлены или  
        // не отменены, не важно прошла у них дата или нет.
        if ($this->isAllFilterAttributesEmpty()) {
            $query->andWhere(['<>', 'stage_id', $deliveredStageId]);
            $query->andWhere(['<>', 'stage_id', $canceledStageId]);
        }
                
                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'client_id', 
                    'user_id', 
                    'stage_id', 
                    'payment_type', 
                    'start_date', 
                    'update_date', 
                    'end_date',
                    'tax', 
                    'items_count', 
                    'total_price', 
                    'total_pay', 
                    'is_paid',
                    'is_deleted', 
                    'is_postponed', 
                    'city_id', 
                    'order_num',
                    'person_num', 
                    'return_sum', 
                    'recipient', 
                    'phone', 
                    'alter_phone', 
                    'address', 
                    'comment', 
                    'delivery_date' => [
                        'asc' => [
                            'isNewStage' => SORT_DESC, 
                            'delivery_date' => SORT_ASC, 
                            'delivery_time' => SORT_ASC
                        ],
                        'desc' => [
                            'isNewStage' => SORT_ASC, 
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
                    'delivery_date' => SORT_ASC, 
                ],
             ],
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'stage_id' => $this->stage_id,
            'payment_type' => $this->payment_type,
            'is_paid' => $this->is_paid,
            'is_deleted' => $this->is_deleted,
            'is_postponed' => $this->is_postponed,
            'delivery_date' => $this->delivery_date,
            'delivery_time' => $this->delivery_time,
        ]);

        $query->andFilterWhere(['like', 'order_num', $this->order_num])
            ->andFilterWhere(['like', 'recipient', $this->recipient])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'alter_phone', $this->alter_phone])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        $this->addDateIntervalConditionsToQuery($query);
        $this->addTimeIntervalConditionsToQuery($query);
        $this->addIntegerValuesIntervalConditionsToQuery($query);
        
        return $dataProvider;
    }
        
    protected function getDateIntervalsRules() 
    {
        return  [
            [
                'startDateFrom', 
                'startDateTo',
                'updateDateFrom',
                'updateDateTo',
                'endDateFrom',
                'endDateTo',
                'deliveryDateFrom',
                'deliveryDateTo'
            ], 
            'date', 
            'format' => self::DATE_FORMAT_YII,
        ];        
    }
    
    protected function getTimeIntervalRules()
    {
        return [
            [
                'deliveryTimeFrom',
                'deliveryTimeTo',
            ],
            'date',
            'format' => self::TIME_SHORT_FORMAT_YII,
        ];
    }
    
    protected function getIntegerValuesIntervalRules()
    {
        return [
            [
                'itemsCountFrom',
                'itemsCountTo',
                'totalPriceFrom',
                'totalPriceTo',
                'taxFrom',
                'taxTo',
                'totalPayFrom',
                'totalPayTo',
                'personNumFrom',
                'personNumTo',
                'returnSumFrom',
                'returnSumTo',
            ],
            'integer',
            'min' => 0,
        ];
    }
        
    protected function addDateIntervalConditionsToQuery($query)
    {
        $startDateFrom = $this->startDateFrom ;
        $startDateTo = $this->startDateTo;
        $updateDateFrom = $this->updateDateFrom;
        $updateDateTo = $this->updateDateTo;
        $endDateFrom = $this->endDateFrom;
        $endDateTo = $this->endDateTo;
        $deliveryDateFrom = $this->deliveryDateFrom;
        $deliveryDateTo = $this->deliveryDateTo;

        
        $dateTimeFormat = self::DATE_FORMAT.' H:i:s';
        if (!empty($startDateFrom)) {
            $startDateFrom = DateTimeHelper::getTimestampFromString($startDateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($startDateTo)) {
            $startDateTo = DateTimeHelper::getTimestampFromString($startDateTo.' 23:59:59', $dateTimeFormat);
        }
        if (!empty($updateDateFrom)) {
            $updateDateFrom = DateTimeHelper::getTimestampFromString($updateDateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($updateDateTo)) {
            $updateDateTo = DateTimeHelper::getTimestampFromString($updateDateTo.' 23:59:59', $dateTimeFormat);
        }
        if (!empty($endDateFrom)) {
            $endDateFrom= DateTimeHelper::getTimestampFromString($endDateFrom.' 00:00:00', $dateTimeFormat);
        }
        if (!empty($endDateTo)) {
            $endDateTo = DateTimeHelper::getTimestampFromString($endDateTo.' 23:59:59', $dateTimeFormat);
        }
        if (!empty($deliveryDateFrom)) {
             $deliveryDateFrom = DateHelper::convertDateToDbFormat($deliveryDateFrom, self::DATE_FORMAT);
        }
        if (!empty($deliveryDateTo)) {
            $deliveryDateTo = DateHelper::convertDateToDbFormat($deliveryDateTo, self::DATE_FORMAT);
        }
   
        $query->andFilterWhere(['>=', 'start_date', $startDateFrom])
            ->andFilterWhere(['<=', 'start_date', $startDateTo]);
        $query->andFilterWhere(['>=', 'update_date', $updateDateFrom])
            ->andFilterWhere(['<=', 'update_date', $updateDateTo]);
        $query->andFilterWhere(['>=', 'end_date', $endDateFrom])
            ->andFilterWhere(['<=', 'end_date', $endDateTo]);
        $query->andFilterWhere(['>=', 'delivery_date', $deliveryDateFrom])
            ->andFilterWhere(['<=', 'delivery_date', $deliveryDateTo]);
        
        return $query;
    }
    
    protected function addTimeIntervalConditionsToQuery($query)
    {
        $deliveryTimeFrom = $this->deliveryTimeFrom;
        $deliveryTimeTo = $this->deliveryTimeTo;
        
        if (!empty($deliveryTimeFrom)) {
             $deliveryTimeFrom  = TimeHelper::convertTimeToDbFormat($deliveryTimeFrom, self::TIME_SHORT_FORMAT);
        }
        if (!empty($deliveryTimeTo)) {
            $deliveryTimeTo = TimeHelper::convertTimeToDbFormat($deliveryTimeTo, self::TIME_SHORT_FORMAT);
        }
        
        $query->andFilterWhere(['>=', 'delivery_time', $deliveryTimeFrom])
            ->andFilterWhere(['<=', 'delivery_time', $deliveryTimeTo]);
    }
        
    protected function addIntegerValuesIntervalConditionsToQuery($query)
    {
        if (!empty($this->itemsCountFrom)) {
            $query->andFilterWhere(['>=', 'items_count', $this->itemsCountFrom]);
        }
        if (!empty($this->itemsCountTo)) {
            $query->andFilterWhere(['<=', 'items_count', $this->itemsCountTo]);
        }
        if (!empty($this->totalPriceFrom)) {
            $query->andFilterWhere(['>=', 'total_price', $this->totalPriceFrom]);
        }
        if (!empty($this->totalPriceTo)) {
            $query->andFilterWhere(['<=', 'total_price', $this->totalPriceTo]);
        }
        if (!empty($this->taxFrom)) {
            $query->andFilterWhere(['>=', 'tax', $this->taxFrom]);
        }
        if (!empty($this->taxTo)) {
            $query->andFilterWhere(['<=', 'tax', $this->taxTo]);
        }
        if (!empty($this->totalPayFrom)) {
            $query->andFilterWhere(['>=', 'total_pay', $this->totalPayFrom]);
        }
        if (!empty($this->totalPayTo)) {
            $query->andFilterWhere(['<=', 'total_pay', $this->totalPayTo]);
        }
        if (!empty($this->personNumFrom)) {
            $query->andFilterWhere(['>=', 'person_num', $this->personNumFrom]);
        }
        if (!empty($this->personNumTo)) {
            $query->andFilterWhere(['<=', 'person_num', $this->personNumTo]);
        }
        if (!empty($this->returnSumFrom)) {
            $query->andFilterWhere(['>=', 'return_sum', $this->returnSumFrom]);
        }
        if (!empty($this->returnSumTo)) {
            $query->andFilterWhere(['<=', 'return_sum', $this->returnSumTo]);
        }
        
        return $query; 
    }
    
    private function isAllFilterAttributesEmpty()
    {
        $attributes = $this->attributes();
        foreach ($attributes as $attribute) {
            if (!empty($this->$attribute)) {
                return false;
            }
        }
        return true;
    }
    
}
