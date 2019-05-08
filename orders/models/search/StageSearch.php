<?php

namespace app\modules\orders\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\orders\models\Stage;

class StageSearch extends Stage
{
    public function rules()
    {
        return [
            [['id', 'sort'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Stage::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }
        $query->andFilterWhere([
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    
}
