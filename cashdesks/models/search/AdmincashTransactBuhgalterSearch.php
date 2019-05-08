<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\AdmincashTransact;
use app\modules\cashdesks\models\ExpenseType;

class AdmincashTransactBuhgalterSearch extends AdmincashTransactBaseSearch
{
    
   
    public function __construct($config = array())
    {
        parent::__construct($config);
    }
 
    public function rules()
    {
        return [
            [
                [
                    'depart_id',
                    'date_create', 
                    'date_end', 
                    'date_edit',
                    'administrator_id', 
                    'user_id', 
                    'user_edit_id',
                    'state',
                ], 
                'integer'
            ],
            [['desc', 'type_value'], 'safe'],
            $this->getDateIntervalsRules(),
        ];
    }
    
    public function search($params)
    {
        $accdepTypeId = ExpenseType::getExpenseTypeAccdepId(); 
        
        $query = AdmincashTransact::find()
            ->joinWith(['banknotes'])
            ->where([
                'type' => AdmincashTransact::TYPE_EXPENSE,
                'type_id' => $accdepTypeId,
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
            'depart_id' => $this->depart_id,
            'state' => $this->state,
            'administrator_id' => $this->administrator_id,
            'user_id' => $this->user_id,
            'user_edit_id' => $this->user_edit_id,
        ]);
        
        
        $query->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'type_value', $this->type_value]);
       
        $this->addDateIntervalsConditionsToQuery($query);
                
        return $dataProvider;
    }
}
