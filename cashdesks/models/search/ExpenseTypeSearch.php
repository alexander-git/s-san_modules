<?php

namespace app\modules\cashdesks\models\search;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\ExpenseType;

class ExpenseTypeSearch extends ExpenseType
{
    public function rules()
    {
        return [
            ['type', 'integer'],
            ['name', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = ExpenseType::find();

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

        $query->andFilterWhere([
            'type' => $this->type,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        return $dataProvider;
    }
    
}
