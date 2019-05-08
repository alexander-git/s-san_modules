<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\AdmincashTransact;


class AdmincashTransactDepartmentSearch extends AdmincashTransactBaseSearch
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
                    'type',
                    'state',
                    'date_create', 
                    'date_end', 
                    'date_edit',
                    'administrator_id', 
                    'user_id', 
                    'user_edit_id',
                ], 
                'integer'
            ],
            [['desc', 'type_value'], 'safe'],
            $this->getDateIntervalsRules(),
        ];
    }
    
    public function search($params)
    {
        $query = AdmincashTransact::find()
            ->joinWith(['banknotes'])
            ->where(['depart_id' => $this->depart_id]);
                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'type',
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
            'type' => $this->type,
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
