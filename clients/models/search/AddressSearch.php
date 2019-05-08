<?php

namespace app\modules\clients\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\clients\models\Address;
use app\modules\clients\models\ClientAddress;

class AddressSearch extends Address
{
    public $ordersCountFrom;
    public $ordersCountTo;
    
    public function rules()
    {
        return [
            [['cityId', 'floor'], 'integer'],
            [['street', 'home', 'appart', 'code', 'entrance', 'name', 'desc'], 'safe'],
            $this->getOrdersCountIntervalRules()
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
         $query = Address::find()
            ->select(['a.*', new Expression('SUM(`ca`.`ordercount`) AS ordersCount')])
            ->from(['a' => Address::tableName()])
            ->joinWith([
                'clientAddresses' => function($q) {
                    $q->from(['ca' => ClientAddress::tableName()]);
                },
            ])
            ->groupBy(['a.id']);
            
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'cityId',
                    'floor',
                    'street', 
                    'home', 
                    'appart', 
                    'code', 
                    'entrance', 
                    'name', 
                    'desc',
                    'ordersCount',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'a.cityId' => $this->cityId,
            'a.floor' => $this->floor,
        ]);

        $query->andFilterWhere(['like', 'a.street', $this->street])
            ->andFilterWhere(['like', 'a.home', $this->home])
            ->andFilterWhere(['like', 'a.appart', $this->appart])
            ->andFilterWhere(['like', 'a.code', $this->code])
            ->andFilterWhere(['like', 'a.entrance', $this->entrance])
            ->andFilterWhere(['like', 'a.name', $this->name])
            ->andFilterWhere(['like', 'a.desc', $this->desc]);

        $this->addOrdersCountIntervalConditionsToQuery($query);
        
        return $dataProvider;
    }
    
    protected function getOrdersCountIntervalRules()
    {
        return [
            [
                'ordersCountFrom',
                'ordersCountTo',
            ],
            'integer',
            'min' => 0,
        ];
    }
    
    protected function addOrdersCountIntervalConditionsToQuery($query)
    {
        if (!empty($this->ordersCountFrom)) {
            $query->andHaving(['>=', 'ordersCount', $this->ordersCountFrom]);
        }
        if (!empty($this->ordersCountTo)) {
            $query->andHaving(['<=', 'ordersCount', $this->ordersCountCountTo]);
        }
        
        return $query; 
    }
    
}
