<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\ExpenseTypeItem;

class ExpenseTypeItemSearch extends ExpenseTypeItem
{
    public function __construct($expenseTypeId, $config = array()) 
    {
        $this->expense_type_id = $expenseTypeId;
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            ['value', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = ExpenseTypeItem::find()
            ->where(['expense_type_id' => $this->expense_type_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'value', $this->value]);
        
        return $dataProvider;
    }
    
}

