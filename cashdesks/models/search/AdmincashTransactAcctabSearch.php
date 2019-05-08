<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\AdmincashTransact;

class AdmincashTransactAcctabSearch extends AdmincashTransactBaseSearch
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
                    'date_create', 
                    'date_end', 
                    'date_edit', 
                    'administrator_id', 
                    'user_id', 
                    'user_edit_id', 
                    'state'
                ], 
                'integer'
            ],
            [['desc'], 'safe'],
            $this->getDateIntervalsRules(),
        ];
    }

    public function search($params)
    {
        $query = AdmincashTransact::find()
            ->joinWith(['banknotes'])
            ->where([
                'depart_id' => $this->depart_id,
                'type' => AdmincashTransact::TYPE_ACCTAB,
            ]);
                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'state',
                    'date_create', 
                    'date_end',
                    'date_edit',
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
            'type_id' => $this->type_id,
            'administrator_id' => $this->administrator_id,
            'user_id' => $this->user_id,
            'user_edit_id' => $this->user_edit_id,
        ]);
      
        
        $query->andFilterWhere(['like', 'desc', $this->desc]);           

        $this->addDateIntervalsConditionsToQuery($query);
        
        return $dataProvider;
    }
}
