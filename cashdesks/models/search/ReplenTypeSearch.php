<?php

namespace app\modules\cashdesks\models\search;

use Yii;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\ReplenType;

class ReplenTypeSearch extends ReplenType
{
    
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = ReplenType::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    
}
