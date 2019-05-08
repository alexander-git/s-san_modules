<?php

namespace app\modules\cashdesks\models\search;


use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\PickercashTransact;

class PickercashTransactPickerSearch extends PickercashTransactBaseSearch
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
                    'state',
                    'date_create', 
                    'date_end', 
                    'user_id', 
                ], 
                'integer'
            ],
            [['desc'], 'safe'],
            $this->getDateIntervalRules(),
        ];
    }
    
    public function search($params)
    {
        $query = PickercashTransact::find()
            ->joinWith(['banknotes'])
            ->where([
                'depart_id' => $this->depart_id,
                'picker_id' => $this->picker_id,
            ]);
                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'type',
                    'state',
                    'date_create', 
                    'date_end',
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
            'state' => $this->state,
        ]);
        
        $query->andFilterWhere(['like', 'desc', $this->desc]);
        $this->addDateIntervalsConditionsToQuery($query);
                
        return $dataProvider;
    }
    
}
