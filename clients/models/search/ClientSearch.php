<?php

namespace app\modules\clients\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\clients\models\Client;
use app\modules\clients\models\ClientAddress;
use app\modules\clients\helpers\DateHelper;

/**
 * ClientSearch represents the model behind the search form about `app\modules\clients\models\Client`.
 */
class ClientSearch extends Client
{
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    public $birthdayFrom;
    public $birthdayTo;
    public $ordersCountFrom;
    public $ordersCountTo;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['cardnum', 'boolean'],
            ['state', 'integer'],
            [
                [
                    'name', 
                    'fullname', 
                    'login', 
                    'email', 
                    'password', 
                    'phone', 
                    'alterPhone', 
                    'description', 
                    'note'
                ], 
                'safe'
            ],
            $this->getBirthdayIntervalRules(),
            $this->getOrdersCountIntervalRules(),
        ];
    }


    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Client::find()
            ->select([
                "c.*", 
                new Expression("SUM(`ca`.`ordercount`) AS ordersCount") 
            ])
            ->from(['c' => Client::tableName()])
            ->joinWith([
                'clientAddresses' => function($q) {
                    $q->from(['ca' => ClientAddress::tableName()]);
                },
            ], false)
            ->groupBy(['c.id']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'name', 
                    'fullname', 
                    'login', 
                    'email', 
                    'password', 
                    'phone', 
                    'alterPhone', 
                    'description', 
                    'note',
                    'state',
                    'birthday',
                    'ordersCount',
                    'cardnum'
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'c.state' => $this->state,
        ]);
        
        //\Yii::error(print_r($params, true));
        if ($this->cardnum !== '') {
            $cardnum = (int) $this->cardnum;
            if ($cardnum) {
                $query->andFilterWhere(['not', 'c.cardnum', null]);
            } else {
                $query->andFilterWhere(['c.cardnum' => null]); 
            }
        }

        $query->andFilterWhere(['like', 'c.name', $this->name])
            ->andFilterWhere(['like', 'c.fullname', $this->fullname])
            ->andFilterWhere(['like', 'c.login', $this->login])
            ->andFilterWhere(['like', 'c.email', $this->email])
            ->andFilterWhere(['like', 'c.password', $this->password])
            ->andFilterWhere(['like', 'c.phone', $this->phone])
            ->andFilterWhere(['like', 'c.alterPhone', $this->alterPhone])
            ->andFilterWhere(['like', 'c.description', $this->description])
            ->andFilterWhere(['like', 'c.note', $this->note]);

        $this->addBirthdayIntervalsConditionsToQuery($query);
        $this->addOrdersCountIntervalConditionsToQuery($query);
        
        
        return $dataProvider;
    }
    
    protected function getBirthdayIntervalRules() 
    {
        return  [
            [
                'birthdayFrom', 
                'birthdayTo', 
            ], 
            'date', 
            'format' => self::DATE_FORMAT_YII,
        ];        
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
    
    protected function addBirthdayIntervalsConditionsToQuery($query)
    {
        $birthdayFrom = $this->birthdayFrom;
        $birthdayTo = $this->birthdayTo;
        
        if (!empty($birthdayFrom)) {
            $birthdayFrom = DateHelper::convertDateToDbFormat($birthdayFrom, self::DATE_FORMAT);
        }
        if (!empty($birthdayTo)) {
            $birthdayTo = DateHelper::convertDateToDbFormat($birthdayTo, self::DATE_FORMAT);
        }
   
        $query->andFilterWhere(['>=', 'c.birthday', $birthdayFrom])
            ->andFilterWhere(['<=', 'c.birthday', $birthdayTo]);
        
        return $query;
    }
    
    protected function addOrdersCountIntervalConditionsToQuery($query)
    {
        if (!empty($this->ordersCountFrom)) {
            $query->andHaving(['>=', 'ordersCount', $this->ordersCountFrom]);
        }
        if (!empty($this->ordersCountTo)) {
            $query->andHaving(['<=', 'ordersCount', $this->ordersCountTo]);
        }
        
        return $query; 
    }
    
    
}
