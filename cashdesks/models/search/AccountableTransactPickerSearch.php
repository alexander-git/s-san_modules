<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\AccountableTransact;


class AccountableTransactPickerSearch extends AccountableTransactBaseSearch
{

    public function __construct($pickerId, $departmentId, $config = array())
    {
        $this->picker_id = $pickerId;
        $this->depart_id = $departmentId;
        parent::__construct($config);
    }
  
    public function rules()
    {
        return [
            [
                [
                    'type',
                    'date_create', 
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
        $query = AccountableTransact::find()
            ->where([
                'depart_id' => $this->depart_id,
                'picker_id' => $this->picker_id,
            ])
            ->andWhere(['in', 'type', array_keys(self::getTypesArrayPicker())]);
                
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
            'user_id' => $this->user_id,
            'type' => $this->type,
        ]);
        
        $query->andFilterWhere(['like', 'desc', $this->desc]);
        $this->addDateIntervalConditionsToQuery($query);
        $this->addSumConditionsToQuery($query);
                
        return $dataProvider;
    }
    
    public static function getTypesArrayPicker()
    {
        $types = parent::getTypesArray();
        unset($types[AccountableTransact::TYPE_REPLEN]);
        unset($types[AccountableTransact::TYPE_RETURN]);
        return $types;
    }
    
}
