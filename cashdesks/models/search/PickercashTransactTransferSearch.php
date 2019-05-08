<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\PickercashTransact;


class PickercashTransactTransferSearch extends PickercashTransactBaseSearch
{

    
    public function __construct($departmentId, $config = array())
    {
        $this->depart_id = $departmentId;
        parent::__construct($config);
    }
 
        
    public function rules()
    {
        return [
            [
                [
                    'state',
                    'date_create', 
                    'date_end',
                    'picker_id',
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
                'type' => PickercashTransact::TYPE_TRANSFER_TO_ADMINCASH,
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
            'state' => $this->state,
            'picker_id' => $this->picker_id,
            'user_id' => $this->user_id,
        ]);
        
        $query->andFilterWhere(['like', 'desc', $this->desc]);
        $this->addDateIntervalsConditionsToQuery($query);
        
        return $dataProvider;
    }
}
