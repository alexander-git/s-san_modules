<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\DocsList;

class DocsListSearch extends DocsList
{
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            ['name', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = DocsList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
