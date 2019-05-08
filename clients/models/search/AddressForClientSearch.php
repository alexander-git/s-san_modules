<?php

namespace app\modules\clients\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\clients\models\Address;
use app\modules\clients\models\ClientAddress;

/**
 * AddressSearch represents the model behind the search form about `app\modules\clients\models\Address`.
 */
class AddressForClientSearch extends Address
{
    public $ordersCountFrom;
    public $ordersCountTo;
       
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cityId', 'floor'], 'integer'],
            [
                [
                    'street', 
                    'home', 
                    'appart', 
                    'code', 
                    'entrance', 
                    'name', 
                    'desc'
                ], 
                'safe'
            ],
            $this->getOrdersCountIntervalRules(),
        ];
    }


    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($clientId, $params)
    {
        $query = Address::find()
            ->select(['a.*', new Expression('SUM(`ca`.`ordercount`) AS ordersCount')])
            ->from(['a' => Address::tableName()])
            ->joinWith([
                'clientAddresses' => function($q) use ($clientId) {
                    $q->from(['ca' => ClientAddress::tableName()])
                        ->andOnCondition(['`ca`.`clientId`' => $clientId]);
                },
            ])
            ->where(['in', 'id', $this->getAddressIds($clientId)])
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
    
    private function getAddressIds($clientId)
    {
        return ClientAddress::find()
            ->select('addressId')
            ->where(['clientId' => $clientId])
            ->column();
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
