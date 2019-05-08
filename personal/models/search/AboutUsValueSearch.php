<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\AboutUsValue;

class AboutUsValueSearch extends AboutUsValue
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
        $query = AboutUsValue::find();

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
