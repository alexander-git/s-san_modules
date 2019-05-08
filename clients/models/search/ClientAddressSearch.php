<?php

namespace app\modules\clients\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\clients\models\Client;
use app\modules\clients\models\ClientAddress;


class ClientAddressSearch extends ClientAddress
{
    
    public $name;
    public $fullname;
    public $ordercountFrom;
    public $ordercountTo;
    
    public function rules()
    {
        return [
            [['clientId', 'addressId', 'ordercount'], 'integer'],
            [['name', 'fullname'], 'safe'],
            $this->getOrdercountIntervalRules()
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }


    public function search($addressId, $params)
    {
        $query = ClientAddress::find()
            ->select(['ca.*', '`c`.`name` AS name' , '`c`.`fullname` AS fullname'])
            ->from(['ca' => ClientAddress::tableName()])
            ->joinWith([
                'client' => function($q) {
                    $q->from(['c' => Client::tableName()]);
                
                },
            ])->where(['ca.addressId' => $addressId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'addressId',
                    'clientId',
                    'ordercount',
                    'name',
                    'fullname',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {;
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ca.clientId' => $this->clientId,
        ]);
        
        $query->andFilterWhere(['like', 'fullname', $this->fullname]);
        
        $this->addOrdercountIntervalConditionsToQuery($query);

        return $dataProvider;
    }
    
    protected function getOrdercountIntervalRules()
    {
        return [
            [
                'ordercountFrom',
                'ordercountTo',
            ],
            'integer',
            'min' => 0,
        ];
    }
    
    protected function addOrdercountIntervalConditionsToQuery($query)
    {

        $query->andFilterWhere(['>=', '`ca`.`ordercount`', $this->ordercountFrom]);
        $query->andFilterWhere(['<=', '`ca`.`ordercount`', $this->ordercountTo]);
      
        return $query; 
    }
    
    
}
